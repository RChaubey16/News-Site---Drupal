var searchBox = document.getElementById("search__box");

var searchIcon = document.getElementById("block-views-block-search-icon-block-1");

var searchCrossIcon = document.getElementById(
  "block-views-block-search-cross-icon-block-1"
);

searchIcon.addEventListener("click", function(){
   console.log("CLick");
   searchBox.style.display = "block";
})

searchCrossIcon.addEventListener("click", function () {
  console.log("Clicked");
  searchBox.style.display = "none";
});
