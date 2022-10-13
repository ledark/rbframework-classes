$('#pagination_<?php echo $this->get_paginateId() ?> ul li').on("click", function(e){
    e.preventDefault();
    
    var toPage = $(this).find('a').text();
    
    if(toPage == '&laquo;') toPage = 1;
    if(toPage == '&raquo;') toPage = '<?php echo $this->maxPages ?>';
    
    var limiteMin = toPage;
    var limiteMax = '<?php echo $this->limiteMax ?>';
    var maxResults = '<?php echo $this->maxResults ?>';
    var maxPages = '<?php echo $this->maxPages ?>';    
   
    limiteMin = parseInt(toPage) * parseInt(limiteMax);
    limiteMin-= parseInt(limiteMax);
    
    
    
    $.get('<?php echo HTTPSITE ?>core/paginate/<?php echo $this->get_paginateId() ?>/'+limiteMin+'/'+limiteMax+'/'+maxResults+'/'+maxPages+'', function(){
        window.location.href = window.location.href + '&usepage=<?php echo $this->get_paginateId() ?>';
    });
    
});