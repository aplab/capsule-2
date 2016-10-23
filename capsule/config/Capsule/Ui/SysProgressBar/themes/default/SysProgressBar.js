function SysProgressBar(path, width, value) 
{
    this.intval = function( mixed_var, base ) {    // Get the integer value of a variable
        // 
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

        var tmp;

        if (typeof(mixed_var) == 'string') {
            tmp = parseInt(mixed_var);
            if (isNaN(tmp)) {
                return 0;
            } else {
                return tmp.toString(base || 10);
            }
        } else if (typeof(mixed_var) == 'number') {
            return Math.floor(mixed_var);
        } else{
            return 0;
        }
    }

    
    value = this.intval(value, 10);
    if (value < 0) {
        value = 0;   
    }
    if (value > 100) {
        value = 100;   
    }
    
    width = this.intval(width, 10);
    if (width < 10) {
        width = 10;   
    }
    if (width > 10000) {
        width = 10000;   
    }
    
    this.path = path;
    this.width = width;
    this.value = value;
    this.height = 20;
    
    this.loadImages = function() {
        var images = new Array();
        images[0] = 'barBg';
        images[1] = 'barBgLeft';
        images[2] = 'barBgRight';
        images[3] = 'railBg';
        images[4] = 'railBgLeft';
        images[5] = 'railBgRight';
        for (var i = 0; i < images.length; i++) {
            eval('this.' + images[i] + ' = new Image();');
            eval('this.' + images[i] + '.src = \'' + this.path + '/img/' + images[i] + '.gif\';');
        }
    }
    
    this.resetElement = function(e) {
        var s = e.style;
        s.padding = '0';   
        s.margin = '0';
        s.border = '0';
        s.display = 'block';
        s.width = '0';
        s.height = '0';
    }
    
    this.resetElementA = function(e) {
        this.resetElement(e);
        var s = e.style;
        s.position = 'absolute';
        s.left = '0';
        s.top = '0';
        
    }
    
    this.createContainer = function() {
        var e = document.createElement('div');
        this.resetElementA(e);
        var s = e.style;
        s.position = 'relative';
        s.width = this.width + 'px';
        s.height = this.height + 'px';
        this.container = e;
    }
    
    this.createRail = function() {
        var e;
        var s;
        e = document.createElement('div');
        this.resetElementA(e);
        s = e.style;
        s.backgroundImage = 'url(' + this.railBg.src + ')';
        s.width = this.width + 'px';
        s.height = this.height + 'px';
        this.rail = e;
        
        e = document.createElement('div');
        this.resetElementA(e);
        s = e.style;
        s.backgroundImage = 'url(' + this.railBgLeft.src + ')';
        s.width = '2px';
        s.height = this.height + 'px';
        s.zIndex = '10';
        this.rail.appendChild(e);
        
        e = document.createElement('div');
        this.resetElementA(e);
        s = e.style;
        s.backgroundImage = 'url(' + this.railBgRight.src + ')';
        s.width = '2px';
        s.height = this.height + 'px';
        s.left = (this.width - 2) + 'px';
        s.zIndex = '20';
        this.rail.appendChild(e);
        
        this.container.appendChild(this.rail);
    }
    
    this.createBar = function() {
        var e;
        var s;
        e = document.createElement('div');
        this.resetElementA(e);
        s = e.style;
        s.width = (this.width - 2) + 'px';
        s.height = (this.height - 2) + 'px';
        s.left = '1px';
        s.top = '1px';
        s.zIndex = '30';
        s.overflow = 'hidden';
        this.barContainer = e;   
        this.rail.appendChild(this.barContainer);
        
        e = document.createElement('div');
        this.resetElementA(e);
        s = e.style;
        s.width = (this.width - 2) + 'px';
        s.height = (this.height - 2) + 'px';
        s.zIndex = '30';
        s.backgroundImage = 'url(' + this.barBg.src + ')';
        this.bar = e; 
        
        e = document.createElement('div');
        this.resetElementA(e);
        s = e.style;
        s.backgroundImage = 'url(' + this.barBgLeft.src + ')';
        s.width = '1px';
        s.height = (this.height - 2) + 'px';
        s.zIndex = '10';
        this.bar.appendChild(e);
        
        e = document.createElement('div');
        this.resetElementA(e);
        s = e.style;
        s.backgroundImage = 'url(' + this.barBgRight.src + ')';
        s.width = '3px';
        s.height = (this.height - 2) + 'px';
        s.zIndex = '20';
        s.left = 'auto';
        s.right = '-1px';
        this.bar.appendChild(e);
        
        
        this.barContainer.appendChild(this.bar);
    }
    
    this.init = function() {
        this.loadImages();
        this.createContainer();
        this.createRail();
        this.createBar();
        this.bar.style.width = this.value + '%';
    }
    
    this.init();
    
    this.setValue = function(value) {
        value = this.intval(value, 10);
        if (value < 0) {
            value = 0;   
        }
        if (value > 100) {
            value = 100;   
        }
        
        this.value = value;
        this.bar.style.width = this.value + '%';
    }
    
    this.show = function(element) {
        element.appendChild(this.container); 
    }
}