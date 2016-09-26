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
loadMoreBtn.addEventListener('click', function(){
    [].forEach.call(document.querySelectorAll('.' + hiddenClass), function(item, idx){
        console.log(item);
        if (idx < maxItems) {
            item.classList.remove(hiddenClass);
        }
        if ( document.querySelectorAll('.' + hiddenClass).length === 0) {
            //if all items exposed hide load more button button
            loadMoreBtn.style.display = 'none';
        }
    });
});