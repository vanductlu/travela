
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("searchInput");
    const suggestionList = document.getElementById("suggestionList");
    
    if (!input) {
        console.error("Không tìm thấy #searchInput");
        return;
    }

    if (!suggestionList) {
        console.error("Không tìm thấy #suggestionList");
        return;
    }

    let debounceTimer;
    const DEBOUNCE_DELAY = 300;

    const API_URL = "http://127.0.0.1:5555/api/search-suggestions";
    
    console.log("Search suggestion initialized");
    console.log("API URL:", API_URL);
    
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    const highlightMatch = (text, keyword) => {
        if (!keyword) return text;
        const esc = escapeRegExp(keyword);
        const regex = new RegExp(`(${esc})`, "gi");
        return text.replace(regex, "<b>$1</b>");
    };

    function showSuggestions(suggestions, keyword) {
        suggestionList.innerHTML = "";
        
        if (!suggestions || suggestions.length === 0) {
            suggestionList.style.display = "none";
            console.log("Không có gợi ý");
            return;
        }

        console.log("Hiển thị", suggestions.length, "gợi ý:", suggestions);

        suggestions.forEach(item => {
            const li = document.createElement("li");
            li.classList.add("list-group-item", "list-group-item-action");
            li.innerHTML = highlightMatch(item, keyword);
            li.style.cursor = "pointer";
            
            li.addEventListener("click", () => {
                input.value = item;
                suggestionList.style.display = "none";
                const form = document.getElementById("searchForm");
                if (form) {
                    form.submit();
                } else {
                    console.error("Không tìm thấy #searchForm");
                }
            });
            
            suggestionList.appendChild(li);
        });

        suggestionList.style.display = "block";
    }

    async function fetchSuggestions(keyword) {
        clearTimeout(debounceTimer);

        if (!keyword || keyword.length < 2) {
            suggestionList.style.display = "none";
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const url = `${API_URL}?keyword=${encodeURIComponent(keyword)}`;
                console.log("Fetching:", url);
                
                const res = await fetch(url);
                
                console.log("Response status:", res.status);
                
                if (!res.ok) {
                    console.error("API error:", res.status, res.statusText);
                    suggestionList.style.display = "none";
                    return;
                }

                const data = await res.json();
                console.log("Data received:", data);
                
                const suggestions = data.suggestions || [];
                showSuggestions(suggestions, keyword);

            } catch (err) {
                console.error("Lỗi khi lấy gợi ý:", err);
                suggestionList.style.display = "none";
            }
        }, DEBOUNCE_DELAY);
    }

    input.addEventListener("input", e => {
        const keyword = e.target.value.trim();
        console.log("⌨️ User typed:", keyword);
        fetchSuggestions(keyword);
    });

    document.addEventListener("click", e => {
        if (!suggestionList.contains(e.target) && e.target !== input) {
            suggestionList.style.display = "none";
        }
    });

    input.addEventListener("keydown", e => {
        const items = suggestionList.querySelectorAll("li");
        let currentFocus = Array.from(items).findIndex(item => item.classList.contains("active"));

        if (e.key === "ArrowDown") {
            e.preventDefault();
            currentFocus++;
            addActive(items, currentFocus);
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            currentFocus--;
            addActive(items, currentFocus);
        } else if (e.key === "Enter") {
            if (currentFocus > -1 && items[currentFocus]) {
                e.preventDefault();
                items[currentFocus].click();
            }
        }
    });

    function addActive(items, index) {
        if (!items || items.length === 0) return;
        removeActive(items);
        
        if (index >= items.length) index = 0;
        if (index < 0) index = items.length - 1;
        
        items[index].classList.add("active");
        const text = items[index].textContent.replace(/<\/?b>/g, '');
        input.value = text;
    }

    function removeActive(items) {
        items.forEach(item => item.classList.remove("active"));
    }
});