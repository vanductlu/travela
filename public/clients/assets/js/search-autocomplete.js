/****************************************
 *  HANDLE SEARCH AI - Lấy gợi ý từ Database    *
 *****************************************/
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("searchInput");
    const suggestionList = document.getElementById("suggestionList");
    
    if (!input) return;

    let debounceTimer;
    const DEBOUNCE_DELAY = 300;
    const API_URL = "http://127.0.0.1:5555/api/search-suggestions";
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
        
        if (suggestions.length === 0) {
            suggestionList.style.display = "none";
            return;
        }

        suggestions.forEach(item => {
            const li = document.createElement("li");
            li.classList.add("list-group-item", "list-group-item-action");
            li.innerHTML = highlightMatch(item, keyword);
            li.style.cursor = "pointer";
            
            li.addEventListener("click", () => {
                input.value = item;
                suggestionList.style.display = "none";
                document.getElementById("searchForm").submit();
            });
            
            suggestionList.appendChild(li);
        });

        suggestionList.style.display = "block";
    }

    async function fetchSuggestions(keyword) {
        clearTimeout(debounceTimer);

        // Nếu không có keyword, ẩn gợi ý
        if (!keyword || keyword.length < 2) {
            suggestionList.style.display = "none";
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const res = await fetch(`${API_URL}?keyword=${encodeURIComponent(keyword)}`);
                
                if (!res.ok) {
                    console.error("API error:", res.status);
                    return;
                }

                const data = await res.json();
                const suggestions = data.suggestions || [];

                // Hiển thị gợi ý từ database
                showSuggestions(suggestions, keyword);

            } catch (err) {
                console.error("Lỗi khi lấy gợi ý:", err);
                suggestionList.style.display = "none";
            }
        }, DEBOUNCE_DELAY);
    }

    // Lắng nghe sự kiện input
    input.addEventListener("input", e => {
        fetchSuggestions(e.target.value.trim());
    });

    // Ẩn gợi ý khi click ra ngoài
    document.addEventListener("click", e => {
        if (!suggestionList.contains(e.target) && e.target !== input) {
            suggestionList.style.display = "none";
        }
    });

    // Xử lý phím mũi tên và Enter
    input.addEventListener("keydown", e => {
        const items = suggestionList.querySelectorAll("li");
        let currentFocus = -1;

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
    }

    function removeActive(items) {
        items.forEach(item => item.classList.remove("active"));
    }
});