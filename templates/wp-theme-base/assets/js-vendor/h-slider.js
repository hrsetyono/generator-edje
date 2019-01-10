/*
  hSlider
  Originally from: https://github.com/electerious/basicSlider

  Turns all children of an element into slider.

  ## EXAMPLE

  HTML:

    <div class="my-slider">
      <article>...</article>
      <article>...</article>
      <article>...</article>
    </div>

  JAVASCRIPT:

    $('.my-slider').hLightbox({
      index: 0,
      arrows: true,
      dots: true,
      beforeChange: ( instance, newIndex, oldIndex ) => {
        // ...
      }
      afterChange: ( instance, newIndex, oldIndex ) => {
        // ...
      }
    });
*/
!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{("undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this).basicSlider=e()}}(function(){return function o(a,d,l){function u(n,e){if(!d[n]){if(!a[n]){var t="function"==typeof require&&require;if(!e&&t)return t(n,!0);if(c)return c(n,!0);var r=new Error("Cannot find module '"+n+"'");throw r.code="MODULE_NOT_FOUND",r}var i=d[n]={exports:{}};a[n][0].call(i.exports,function(e){return u(a[n][1][e]||e)},i,i.exports,o,a,d,l)}return d[n].exports}for(var c="function"==typeof require&&require,e=0;e<l.length;e++)u(l[e]);return u}({1:[function(e,n,t){"use strict";n.exports=function(n,e,t){var r=e-n+1,i=t-n;return function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:0;return 0<=(i=(i+e)%r)&&(i=0+i),i<0&&(i=r+i),n+i}}},{}],2:[function(e,n,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.create=void 0;var r,i=e("count-between"),v=(r=i)&&r.__esModule?r:{default:r};var h="left",g=function(e){"function"==typeof e.stopPropagation&&e.stopPropagation(),"function"==typeof e.preventDefault&&e.preventDefault()},E=function(e,n){var t=document.createElement("button");return e=e===h?"left":"right",t.classList.add("hSlider__arrow"),t.classList.add("hSlider__arrow--"+e),t.onclick=function(e){n(),g(e)},t},b=function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"",n=document.createElement("div");return n.classList.add("hSlider__slide"),n.innerHTML=e,n},_=function(e,n,t,r){n.style.transform="translateX(-"+100/r*t+"%)",e.forEach(function(e){return e.classList.remove("active")}),e[t].classList.add("active")},u=function(e,n,i,t){var r,o,a,d,l,u,c,s,f,p=(0,v.default)(0,i.length()-1,t.index),m={};return m.slideElems=n.map(b),m.dotElems=n.map(function(e,n){return t=i.goto.bind(null,n),(r=document.createElement("button")).classList.add("hSlider__dot"),r.onclick=function(e){t(),g(e)},r;var t,r}),m.dotsElem=function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:[],n=document.createElement("div");return n.classList.add("hSlider__dots"),e.forEach(function(e){return n.appendChild(e)}),n}(m.dotElems),m.slidesElem=function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:[],n=document.createElement("div");return n.classList.add("hSlider__slides"),n.style.width=100*e.length+"%",e.forEach(function(e){return n.appendChild(e)}),n}(m.slideElems),m.containerElem=(r=m.slidesElem,(o=document.createElement("div")).classList.add("hSlider__container"),o.appendChild(r),o),m.arrowLeftElem=E(h,i.prev),m.arrowRightElem=E("right",i.next),_(m.dotElems,m.slidesElem,p(),i.length()),a=e,l=t,u=(d=m).arrowLeftElem,c=d.arrowRightElem,s=d.dotsElem,f=d.containerElem,a.classList.add("hSlider"),a.innerHTML="",a.appendChild(f),!0===l.arrows&&(a.appendChild(u),a.appendChild(c)),!0===l.dots&&a.appendChild(s),{c:p,refs:m}};t.create=function(e,n,t){t=function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{};return e=Object.assign({},e),!1===Number.isFinite(e.index)&&(e.index=0),!1!==e.arrows&&(e.arrows=!0),!1!==e.dots&&(e.dots=!0),"function"!=typeof e.beforeChange&&(e.beforeChange=function(){}),"function"!=typeof e.afterChange&&(e.afterChange=function(){}),e}(t);var r=null,i=null,o=function(){return n.length},a=function(e){var n=1<arguments.length&&void 0!==arguments[1]?arguments[1]:r();if(!1===t.beforeChange(d,e,n))return!1;r=(0,v.default)(0,o()-1,e),_(i.dotElems,i.slidesElem,r(),o()),t.afterChange(d,e,n)},d={element:function(){return e},length:o,current:function(){return r()},goto:a,prev:function(){var e=r(),n=r(-1);a(n,e)},next:function(){var e=r(),n=r(1);a(n,e)}},l=u(e,n,d,t);return r=l.c,i=l.refs,d}},{"count-between":1}]},{},[2])(2)});


// CUSTOM HELPER
jQuery.fn.extend( {
  hSlider: function( args ) {
    var _args = args || {};
    var _slider = this.get(0);
    var _content = Array.prototype.map.call( _slider.children, (slide, i) => slide.outerHTML ); // get the HTML of every slides
    var _currentItemsPerSlide = null;


    // if responsive, rerun when window resized.
    if( _args.responsive ) {
      let timer = null;

      window.addEventListener('resize', () => {
        clearTimeout( timer );
        timer = setTimeout( initSlider, 100 );
      });
    }

    initSlider(); // initiate the slider

    
    /////


    function initSlider() {
      let itemsPerSlide = _args.itemsPerSlide || 1;

      // check if responsive
      if( _args.responsive ) {
        const width = window.innerWidth || window.outerWidth;
        var breakpoints = Object.keys( _args.responsive ).reverse();

        // check if current width already below the breakpoint
        for( var i = 0, len = breakpoints.length; i < len; i++ ) {
          if( width < breakpoints[i] ) {
            itemsPerSlide = _args.responsive[ breakpoints[i] ];
            break;
          }
        }
      }
      
      // if current itemsPerSlide already the same, abort
      if( _currentItemsPerSlide === itemsPerSlide ) { return false; }
      // set new itemsPerSlide
      _currentItemsPerSlide = itemsPerSlide;
      
      // create slider
      basicSlider.create(
        _slider,
        groupSlides( _content, itemsPerSlide )
      );
    }


    /*
      Group the content of slider based on itemsPerSlide

      @param content (array) - Array of each slides content.
      @param itemsPerSlides (int) - Number of items per slide.

      @return array - Grouped array
    */
    function groupSlides( content, itemsPerSlides ) {
      return content.reduce( ( groups, item, i ) => {
    
        const group = Math.floor( i / itemsPerSlides );
        const existing = groups[ group ] == null ? '' : groups[ group ];

        groups[ group ] = existing + item;
        return groups;
      }, []);
    }

  }
});