
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('main-search');
    const searchResultsContainer = document.createElement('div');
    searchResultsContainer.className = 'search-results-dropdown';
    searchResultsContainer.style.display = 'none';
    searchInput.parentNode.appendChild(searchResultsContainer);
    let searchIcon = document.getElementById('search-icon-top');
    
    let debounceTimer;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (query.length < 2) {
            searchResultsContainer.style.display = 'none';
            searchIcon.classList.remove('fa-spinner', 'fa-spin');
            searchIcon.classList.add('fa-search');
            return;
        }

        searchIcon.classList.remove('fa-search');
        searchIcon.classList.add('fa-spinner', 'fa-spin');
  
        debounceTimer = setTimeout(() => {
            fetch('/app/user/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ query: query })
            })
            .then(response => response.json())
            .then(data => {
                searchIcon.classList.remove('fa-spinner', 'fa-spin');
                searchIcon.classList.add('fa-search');

                displaySearchResults(data);
            })
            .catch(error => {
                searchIcon.classList.remove('fa-spinner', 'fa-spin');
                searchIcon.classList.add('fa-search');

                console.error('Error:', error);
            });
        }, 300);
    });
    
    function displaySearchResults(results) {
        searchResultsContainer.innerHTML = '';
        
        const hasResults = 
            results.chats.length > 0 || 
            results.contents.length > 0 || 
            results.templates.length > 0;
        
        // Create section with header
        const searchSection = document.createElement('div');
        searchSection.className = 'search-section';
        
        const searchHeader = document.createElement('h4');
        searchHeader.textContent = 'Search Results';
        searchHeader.className = 'search-section-header';
        searchSection.appendChild(searchHeader);
        
        if (!hasResults) {
            const noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'no-results-message';
            noResultsMessage.textContent = 'No results found, please try with another word';
            
            searchSection.appendChild(noResultsMessage);
            searchResultsContainer.appendChild(searchSection);
            searchResultsContainer.style.display = 'block';
            return;
        }
        
        // Create a single list for all results
        const resultsList = document.createElement('ul');
        resultsList.className = 'search-results-list';
        
        // Add templates
        results.templates.forEach(template => {
            const item = document.createElement('li');
            item.className = 'search-result-item';
            
            const link = document.createElement('a');
            link.href = template.url;
            
            const iconContainer = document.createElement('span');
            iconContainer.className = 'icon-container';
            iconContainer.innerHTML = template.icon || '<i class="fa-solid fa-file-lines"></i>';
            
            const nameSpan = document.createElement('span');
            nameSpan.className = 'result-name';
            nameSpan.textContent = template.name;
            
            const typeSpan = document.createElement('span');
            typeSpan.className = 'result-type';
            typeSpan.textContent = 'Template';
            
            link.appendChild(iconContainer);
            link.appendChild(nameSpan);
            link.appendChild(typeSpan);
            item.appendChild(link);
            resultsList.appendChild(item);
        });
        
        // Add chats
        results.chats.forEach(chat => {
            const item = document.createElement('li');
            item.className = 'search-result-item';
            
            const link = document.createElement('a');
            link.href = chat.url;
            
            const iconContainer = document.createElement('span');
            iconContainer.className = 'icon-container';
            if (chat.logo) {
                const img = document.createElement('img');
                img.src = chat.logo;
                img.className = 'chat-logo';
                img.alt = chat.name;
                iconContainer.appendChild(img);
            } else {
                iconContainer.innerHTML = '<i class="fa-solid fa-comments"></i>';
            }
            
            const nameSpan = document.createElement('span');
            nameSpan.className = 'result-name';
            nameSpan.textContent = chat.name;
            
            const typeSpan = document.createElement('span');
            typeSpan.className = 'result-type';
            typeSpan.textContent = 'Chat Assistant';
            
            link.appendChild(iconContainer);
            link.appendChild(nameSpan);
            link.appendChild(typeSpan);
            item.appendChild(link);
            resultsList.appendChild(item);
        });
        
        // Add contents
        results.contents.forEach(content => {
            const item = document.createElement('li');
            item.className = 'search-result-item';
            
            const link = document.createElement('a');
            link.href = content.url;
            
            const iconContainer = document.createElement('span');
            iconContainer.className = 'icon-container';
            iconContainer.innerHTML = content.icon || '<i class="fa-solid fa-file-lines"></i>';
            
            const nameSpan = document.createElement('span');
            nameSpan.className = 'result-name';
            nameSpan.textContent = content.title || 'Untitled Content';
            
            const typeSpan = document.createElement('span');
            typeSpan.className = 'result-type';
            typeSpan.textContent = 'Document';
            
            link.appendChild(iconContainer);
            link.appendChild(nameSpan);
            link.appendChild(typeSpan);
            item.appendChild(link);
            resultsList.appendChild(item);
        });
        
        searchSection.appendChild(resultsList);
        searchResultsContainer.appendChild(searchSection);
        searchResultsContainer.style.display = 'block';
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !searchResultsContainer.contains(event.target)) {
            searchResultsContainer.style.display = 'none';
        }
    });
});