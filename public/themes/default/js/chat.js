/**
 * Enhanced Chat Implementation
 * Processes streaming markdown content with smooth word-by-word animations
 */

class DOMDiffer {
    constructor() {
        this.parser = new DOMParser();
    }

    /**
     * Updates the DOM with new HTML content, preserving animations for root updates.
     * @param {string} newHTML - The new HTML content to render.
     * @param {HTMLElement} container - The target DOM element.
     * @param {boolean} isRoot - If true, updates the root container directly.
     */
    updateDOM(newHTML, container, isRoot = false) {
        if (!container) return;

        const newDOM = this.parser.parseFromString(newHTML, 'text/html').body;
        if (isRoot) {
            const animatedElements = Array.from(container.querySelectorAll('.animated-word'));
            container.innerHTML = newHTML;
            this.reapplyAnimations(animatedElements, container);
        } else {
            this.reconcileNodes(newDOM, container, container.parentNode);
        }
    }

    /**
     * Reapplies animation styles to matching elements based on data-word-id.
     * @param {HTMLElement[]} animatedElements - Previously animated elements.
     * @param {HTMLElement} container - The updated container.
     */
    reapplyAnimations(animatedElements, container) {
        animatedElements.forEach(elem => {
            const id = elem.dataset.wordId;
            if (id) {
                const newElem = container.querySelector(`[data-word-id="${id}"]`);
                if (newElem) {
                    newElem.classList.add('animated');
                    newElem.style.opacity = '1';
                }
            }
        });
    }

    /**
     * Reconciles new and old DOM nodes, updating the structure efficiently.
     * @param {Node} newNode - The new node to apply.
     * @param {Node} oldNode - The existing node to update.
     * @param {Node} parent - The parent node for appending/removing.
     */
    reconcileNodes(newNode, oldNode, parent) {
        if (!parent) return;

        if (!oldNode) return parent.appendChild(this.cloneWithAnimations(newNode));
        if (!newNode) return parent.removeChild(oldNode);

        if (newNode.nodeType !== oldNode.nodeType || newNode.nodeName !== oldNode.nodeName) {
            return parent.replaceChild(this.cloneWithAnimations(newNode), oldNode);
        }

        this.updateAttributes(oldNode, newNode);
        this.reconcileChildren(newNode, oldNode);
    }

    updateAttributes(oldNode, newNode) {
        const oldAttrs = Array.from(oldNode.attributes);
        const newAttrs = Array.from(newNode.attributes);

        oldAttrs.forEach(attr => {
            if (!newNode.hasAttribute(attr.name)) oldNode.removeAttribute(attr.name);
        });

        newAttrs.forEach(attr => {
            if (oldNode.getAttribute(attr.name) !== attr.value) {
                oldNode.setAttribute(attr.name, attr.value);
            }
        });
    }

    reconcileChildren(newNode, oldNode) {
        const newChildren = Array.from(newNode.childNodes);
        const oldChildren = Array.from(oldNode.childNodes);
        const max = Math.max(newChildren.length, oldChildren.length);

        for (let i = 0; i < max; i++) {
            this.reconcileNodes(newChildren[i], oldChildren[i], oldNode);
        }
    }

    cloneWithAnimations(node) {
        const clone = node.cloneNode(true);
        if (node.classList?.contains('animated')) {
            clone.classList.add('animated');
            clone.style.opacity = '1';
        }
        return clone;
    }
}

class EnhancedStreamProcessor {
    constructor(options = {}) {
        this.options = {
            container: null,
            scrollContainer: null,
            animationSpeed: 20, // Milliseconds between word animations
            ...options
        };

        if (!this.options.container) throw new Error('Container element is required');

        // Initialize properties
        this.buffer = '';
        this.differ = new DOMDiffer();
        this.lastRenderedContent = '';
        this.animationQueue = [];
        this.isAnimating = false;
        this.wordCounter = 0;

        this.options.container.classList.add('markdown-content', 'streaming');
        this.observer = new MutationObserver(() => this.processAnimations());
        this.observer.observe(this.options.container, {
            childList: true,
            subtree: true,
            characterData: true
        });

        this.initializeMarked();
    }

    /**
     * Configures the marked library with custom rendering for animations and highlighting.
     */
    initializeMarked() {
        if (!window.marked) {
            throw new Error('Marked library is required');
        }

        const renderer = new marked.Renderer();

        renderer.code = (code, lang) => {
            const language = hljs.getLanguage(lang) ? lang : 'plaintext';
            const highlighted = hljs.highlight(code, { language }).value;
            return `<pre data-language="${language}"><code class="hljs ${language}">${highlighted}</code></pre>`;
        };

        renderer.text = text => {
            if (!text || typeof text !== 'string') return '';
            return text.split(/(\s+)/).map(word => {
                if (!word.trim()) return word;
                this.wordCounter++;
                return `<span class="animated-word" data-word-id="${this.wordCounter}" style="opacity: 0">${word}</span>`;
            }).join('');
        };

        marked.setOptions({
            renderer,
            breaks: true,
            gfm: true,
            headerIds: false,
            mangle: false
        });
    }

    /**
     * Renders the buffer as markdown, updates the DOM, and triggers animations.
     */
    renderContent() {
        try {
            let content = this.buffer;
            const codeBlockCount = (content.match(/```/g) || []).length;
            if (codeBlockCount % 2 !== 0) content += '\n```';

            const html = DOMPurify.sanitize(marked.parse(content), {
                ADD_TAGS: ['pre', 'code', 'span'],
                ADD_ATTR: ['class', 'data-word-id', 'data-language']
            });

            if (html !== this.lastRenderedContent) {
                this.differ.updateDOM(html, this.options.container, true);
                this.lastRenderedContent = html;
                this.handleScroll();
                this.processAnimations();
            }
        } catch (error) {
            console.error('Error rendering content:', error);
            this.options.container.textContent = 'Error rendering content.';
        }
    }

    /**
     * Processes an incoming text chunk and triggers rendering.
     * @param {string} chunk - The new text chunk to append.
     */
    processStreamChunk(chunk) {
        if (typeof chunk !== 'string') {
            console.warn('Chunk must be a string, received:', typeof chunk);
            return;
        }
        this.buffer += chunk;
        this.renderContent();
    }

    /**
     * Animates unanimated words in the container sequentially.
     */
    processAnimations() {
        if (this.isAnimating) return;
        this.isAnimating = true;

        const words = Array.from(this.options.container.querySelectorAll('.animated-word:not(.animated)'));
        this.animationQueue.push(...words);
        this.animateNextWord();
    }

    animateNextWord() {
        if (this.animationQueue.length === 0) {
            this.isAnimating = false;
            return;
        }

        const word = this.animationQueue.shift();
        if (word) {
            word.classList.add('animated');
            word.style.transition = 'opacity 0.2s ease-in-out';
            word.style.opacity = '1';
        }

        setTimeout(() => this.animateNextWord(), this.options.animationSpeed);
    }

    /**
     * Scrolls the container to the bottom if near the end.
     */
    handleScroll() {
        const scroller = this.options.scrollContainer || this.options.container;
        if (!scroller) return;

        const isNearBottom = scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight <= 100;
        if (isNearBottom) {
            requestAnimationFrame(() => {
                scroller.scrollTop = scroller.scrollHeight;
            });
        }
    }

    /**
     * Cleans up resources when the processor is no longer needed.
     */
    destroy() {
        this.observer.disconnect();
        this.animationQueue = [];
        this.isAnimating = false;
    }
}