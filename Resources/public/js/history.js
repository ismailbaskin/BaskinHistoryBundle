//cache params
var parent = document.querySelector('#logHistory'),
    items  = parent.querySelectorAll('.history-log-wrap'),
    loadMoreBtn =  document.querySelector('#loadMore'),
    maxItems = 5,
    hiddenClass = "hidden";

[].forEach.call(items, function(item, idx){
    if (idx > maxItems ) {
        item.classList.add(hiddenClass);
    }
});

//on button click load related data
if(loadMoreBtn !== null){
    loadMoreBtn.addEventListener('click', function(){
        [].forEach.call(document.querySelectorAll('.' + hiddenClass), function(item, idx){
            if (idx < maxItems) {
                item.classList.remove(hiddenClass);
            }
            if ( document.querySelectorAll('.' + hiddenClass).length === 0) {
                loadMoreBtn.style.display = 'none';
            }
        });
    });
}
