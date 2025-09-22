// Markdown and Code Rendering for Chat Streams in Laravel Chat Application

// Global tracking variables for streaming state
let streamBuffer = {};
let isStreaming = {};
let completedStreams = new Set();

// Initialize markdown-it for formatting
const md = window.markdownit({
  html: false,
  xhtmlOut: false,
  breaks: true,
  langPrefix: 'language-',
  linkify: true,
  typographer: true,
  highlight: function(str, lang) {
    if (lang && hljs.getLanguage(lang)) {
      try {
        return '<pre class="hljs"><code class="language-' + 
               lang + '">' +
               hljs.highlight(str, { language: lang, ignoreIllegals: true }).value +
               '</code></pre>';
      } catch (__) {}
    }
    return '<pre class="hljs"><code>' + md.utils.escapeHtml(str) + '</code></pre>';
  }
}).use(window.markdownitTable);

// Setup special rendering for tables
md.renderer.rules.table_open = function() {
  return '<table class="md-table">';
};

// Configure cursor animation
const cursorChar = '▌';
const cursorClass = 'streaming-cursor';
const cursorBlinkClass = 'cursor-blink';



// Process incoming stream chunks
function processStreamChunk(elementId, text) {
  if (!streamBuffer[elementId]) {
    streamBuffer[elementId] = '';
    isStreaming[elementId] = true;
  }
  
  // Append new text to the buffer
  streamBuffer[elementId] += text;
  
  // Render the current state with a cursor
  renderMarkdown(elementId, streamBuffer[elementId], false);
}

// Mark a stream as complete
function completeStream(elementId) {
  if (streamBuffer[elementId]) {
    // Render the final content without cursor
    renderMarkdown(elementId, streamBuffer[elementId], true);
    
    // Clean up
    isStreaming[elementId] = false;
    completedStreams.add(elementId);
  }
}

// Render markdown with proper formatting and optional cursor
function renderMarkdown(elementId, text, isComplete = false) {
  const element = document.getElementById(elementId);
  if (!element) return;
  
  try {
    // Convert markdown to HTML
    const renderedHtml = md.render(text);
    
    // Add cursor for ongoing streams, or just the final content for completed ones
    if (isComplete) {
      element.innerHTML = renderedHtml;
      
      // Clean up streaming state
      isStreaming[elementId] = false;
      completedStreams.add(elementId);
    } else {
      element.innerHTML = renderedHtml + 
        `<span class="${cursorClass} ${cursorBlinkClass}">${cursorChar}</span>`;
      isStreaming[elementId] = true;
    }
    
    // Apply formatting to code blocks and tables
    applyFormatting(element);
    
    // Scroll container to show latest content
    const chatContainer = document.getElementById('chat-container');
    if (chatContainer) {
      chatContainer.scrollTop = chatContainer.scrollHeight;
    }
  } catch (error) {
    console.error("Markdown rendering error:", error);
    element.innerHTML = `<p>${text}</p>`;
  }
}

// Apply syntax highlighting to code blocks and format tables
function applyFormatting(element) {
  // Highlight code blocks
  element.querySelectorAll('pre code').forEach(block => {
    if (!block.classList.contains('hljs-highlighted')) {
      hljs.highlightBlock(block);
      block.classList.add('hljs-highlighted');
      
      // Add copy button to code blocks
      const pre = block.parentNode;
      if (pre && !pre.querySelector('.code-header')) {
        const header = document.createElement('div');
        header.className = 'code-header';
        
        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-code-button';
        copyBtn.textContent = 'Copy';
        copyBtn.onclick = function() {
          navigator.clipboard.writeText(block.textContent)
            .then(() => {
              copyBtn.textContent = 'Copied!';
              setTimeout(() => { copyBtn.textContent = 'Copy'; }, 2000);
            });
        };
        
        header.appendChild(copyBtn);
        pre.insertBefore(header, block);
      }
    }
  });

  // Format tables
  element.querySelectorAll('table:not(.formatted)').forEach(table => {
    table.classList.add('table', 'table-bordered', 'table-striped', 'mt-3', 'formatted');
  });
}

// Handle EventSource messages from OpenAI streaming
function handleOpenAIStream(event, messageId) {
  if (event.data === '[DONE]') {
    completeStream(messageId);
  } else {
    try {
      const data = JSON.parse(event.data);
      if (data.choices && data.choices[0].delta && data.choices[0].delta.content) {
        // Process content from OpenAI stream
        processStreamChunk(messageId, data.choices[0].delta.content);
      }
    } catch (error) {
      console.error('Error parsing OpenAI stream data:', error);
    }
  }
}

// Handle EventSource messages from Anthropic streaming
function handleAnthropicStream(event, messageId) {
  if (event === '[DONE]') {
    completeStream(messageId);
  } else {
    try {
      // For Anthropic, we get raw text chunks without the <br/> conversion
      processStreamChunk(messageId, event);
    } catch (error) {
      console.error('Error parsing Anthropic stream data:', error);
    }
  }
}

// Add these styles to your page
document.addEventListener('DOMContentLoaded', function() {
  // Inject required styles if not already present
  if (!document.getElementById('markdown-stream-styles')) {
    const styleEl = document.createElement('style');
    styleEl.id = 'markdown-stream-styles';
    styleEl.innerHTML = `
      .streaming-cursor {
        display: inline-block;
        vertical-align: middle;
        animation: blink 1s step-start infinite;
      }
      
      @keyframes blink {
        50% { opacity: 0; }
      }
      
      .cursor-blink {
        margin-left: 2px;
      }
      
      pre.hljs {
        padding: 1rem;
        border-radius: 8px;
        margin: 1rem 0;
        overflow-x: auto;
        position: relative;
      }
      
      pre.hljs code {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
        font-size: 0.9rem;
        line-height: 1.5;
      }
      
      .code-header {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        display: flex;
        gap: 0.5rem;
      }
      
      .copy-code-button {
        background-color: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 4px;
        color: #ffffff;
        padding: 4px 8px;
        font-size: 0.7rem;
        cursor: pointer;
        transition: background-color 0.2s;
      }
      
      .copy-code-button:hover {
        background-color: rgba(255, 255, 255, 0.2);
      }
      
      .md-table {
        border-collapse: collapse;
        margin: 1rem 0;
        width: 100%;
        overflow-x: auto;
        display: block;
      }
      
      .md-table th, .md-table td {
        padding: 0.75rem;
        text-align: left;
        border: 1px solid #dee2e6;
      }
      
      .md-table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
        background-color: rgba(0, 0, 0, 0.05);
      }
      
      .md-table tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
      }
      
      .dark-mode pre.hljs {
        background-color: #1e1e3f !important;
        border: 1px solid #2d2d54;
      }
      
      .dark-mode .md-table thead th {
        background-color: #2d2d54;
        color: #f8f9fa;
      }
      
      .dark-mode .md-table tbody tr:nth-of-type(odd) {
        background-color: #1e1e3f;
      }
      
      .dark-mode .md-table td, .dark-mode .md-table th {
        border-color: #2d2d54;
      }
      
      blockquote {
        border-left: 4px solid #6c757d;
        padding-left: 1rem;
        margin-left: 0;
        color: #6c757d;
      }
      
      .dark-mode blockquote {
        border-left-color: #495057;
        color: #ced4da;
      }
      
      .msg-text ul, .msg-text ol {
        padding-left: 1.5rem;
      }
      
      .markdown-content {
        width: 100%;
      }
    `;
    document.head.appendChild(styleEl);
  }
});