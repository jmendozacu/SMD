/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$j(document).ready( function() { 
    $j('.block-account .block-title').each(function() {
        $j(this).toggleSingle();
    });
    
    enquire.register('(max-width: ' + bp.medium + 'px)', {
        setup: function () {
            this.toggleElements = $j(
                // This selects the menu on the My Account and CMS pages
                '.col-left-first .block:not(.block-layered-nav) .block-title, ' +
                    '.col-left-first .block-layered-nav .block-subtitle--filter, ' +
                    '.sidebar:not(.col-left-first) .block .block-title'
            );
        },
        match: function () {
            this.toggleElements.toggleSingle({destruct: true});
            this.toggleElements.toggleSingle();
        },
        unmatch: function () {
            this.toggleElements.toggleSingle({destruct: true});
            this.toggleElements.toggleSingle();
        }
    });
});