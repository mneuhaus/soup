$.widget("custom.cardExpandable",{options:{},_create:function(){var e=this;this.element.on("click",".card-header",function(t){t.preventDefault(),e.toggle()})},toggle:function(){this.element.find(".card-body").slideToggle()}});