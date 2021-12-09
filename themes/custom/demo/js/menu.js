var menuBlock = document.getElementById('block-views-block-header-block-2-sign-in-social-icons-block-1');

var hamMenuItems = document.getElementById('block-views-block-hamburger-menu-list-block-1');
var closeIcon = document.getElementById('block-views-block-close-icon-view-block-1');

var flag = "off";
menuBlock.addEventListener('click', function(){

    if (flag == "off"){
        hamMenuItems.style.display = 'block';
        closeIcon.style.display = 'block';
        flag = 'on';
    } else if(flag == "on"){
        hamMenuItems.style.display = 'none';
        flag = 'off';
    }
    
});

closeIcon.addEventListener('click', function(){
    hamMenuItems.style.display = 'none';
    closeIcon.style.display = 'none';
        flag = 'off';
})