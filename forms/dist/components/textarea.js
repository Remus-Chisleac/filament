function s({initialHeight:i,shouldAutosize:t,state:r}){return{state:r,wrapperEl:null,init:function(){this.wrapperEl=this.$el.parentNode,this.setInitialHeight(),t||this.setUpResizeObserver(),t&&this.$watch("state",()=>{this.resize()})},setInitialHeight:function(){this.$el.scrollHeight<=0||(this.wrapperEl.style.height=i+"rem")},resize:function(){if(this.setInitialHeight(),this.$el.scrollHeight<=0)return;let e=this.$el.scrollHeight+"px";this.wrapperEl.style.height!==e&&(this.wrapperEl.style.height=e)},setUpResizeObserver:function(){new ResizeObserver(()=>{this.wrapperEl.style.height=this.$el.style.height}).observe(this.$el)}}}export{s as default};
