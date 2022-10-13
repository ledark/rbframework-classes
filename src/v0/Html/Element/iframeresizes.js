setInterval(function(){
    var iframe = {
        href: document.getElementById("{id}").contentWindow.location.href,
        innerHeight: document.getElementById("{id}").contentWindow.document.body.offsetHeight,
    };
    
    document.getElementById("{id}").style.height = iframe.innerHeight+'px';
    
}, 2000 );