document.observe("dom:loaded", function() {
    document.getElementById('nav').innerHTML = document.getElementById('nav').innerHTML.replace('*Content Manager*', '<span class="advi-menu-main">Content Manager</span>');
});