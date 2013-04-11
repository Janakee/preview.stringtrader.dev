/*
Classipress Rated Author
Current Version: 1.2
Plugin Author: Julio Gallegos
Author URL: http://myclassipro.com
*/

jQuery.fn.ratings=function(stars,initialRating){var elements=this;return this.each(function(){if(!initialRating)initialRating=0;var containerElement=this;var container=jQuery(this);var starsCollection=Array();containerElement.rating=initialRating;container.css("overflow","auto");for(var starIdx=0;starIdx<stars;starIdx++){var starElement=document.createElement("div");var star=jQuery(starElement);starElement.rating=starIdx+1;star.addClass("jquery-ratings-star");if(starIdx<initialRating)star.addClass("jquery-ratings-full");
container.append(star);starsCollection.push(star);star.click(function(){elements.triggerHandler("ratingchanged",{rating:this.rating});containerElement.rating=this.rating});star.mouseenter(function(){for(var index=0;index<this.rating;index++)starsCollection[index].addClass("jquery-ratings-full");for(var index=this.rating;index<stars;index++)starsCollection[index].removeClass("jquery-ratings-full")});container.mouseleave(function(){for(var index=0;index<containerElement.rating;index++)starsCollection[index].addClass("jquery-ratings-full");
for(var index=containerElement.rating;index<stars;index++)starsCollection[index].removeClass("jquery-ratings-full")})}})};
