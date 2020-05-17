if(ProgressBar == undefined) {
    var ProgressBar = [];
}

ProgressBar['{id}'] = {
    data: [],
    getFromStream: function() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '{httpStream}', true);
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                ProgressBar['{id}'].data = JSON.parse(this.responseText);
                ProgressBar['{id}'].update();
            }
        };
        xhr.send();            
    },
    update: function() {
        if(ProgressBar['{id}'].data.refresh != '') {
            window.location.href = ProgressBar['{id}'].data.refresh;
        }
        value = ProgressBar['{id}'].data.value;
        document.getElementById('{id}').style.width = value + '%';
        document.getElementById('{id}').innerHTML = ProgressBar['{id}'].data.min + '/' + ProgressBar['{id}'].data.max
        document.getElementsByClassName('{id}')[0].innerHTML = ProgressBar['{id}'].data.text;
        if(value < 100) {
            setTimeout(function(){
                ProgressBar['{id}'].getFromStream();
            }, 2000);
        }
    },
};

setTimeout(function(){
    ProgressBar['{id}'].getFromStream();
}, 2000);
            
      
        
