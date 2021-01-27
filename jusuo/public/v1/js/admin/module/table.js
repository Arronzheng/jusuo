$('.c-table-filter-container').on('click',function(e){
    let isFold = $(this).hasClass('fold');
    if(isFold){
        $(this).removeClass('fold');
        $('#fold-spread').find('div').removeClass('hidden');
        $('#fold-spread .spread').addClass('hidden');
    }
});

$('#fold-spread .spread').on('click',function(e){
    e.stopPropagation();
    //展开filter
    let filterContainer = $(this).parents('.c-table-filter-container');
    let parent = $(this).parent();
    filterContainer.removeClass('fold');
    parent.find('div').removeClass('hidden');
    $(this).addClass('hidden');
});

$('#fold-spread .fold').on('click',function(e){
    e.stopPropagation();
    //收起filter
    let filterContainer = $(this).parents('.c-table-filter-container');
    let parent = $(this).parent();
    filterContainer.addClass('fold');
    parent.find('div').removeClass('hidden');
    $(this).addClass('hidden');
});

