/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
        config.forcePasteAsPlainText =true;
        config.pasteFromWordRemoveStyle = true;  
        config.extraPlugins += (config.extraPlugins ?
        ',firstImg,title1,subject_desc,video,play,share1,down1,css' : 
          'firstImg,title1,subject_desc,video,play,share1,down1,css');
	/*config.extraPlugins += (config.extraPlugins ?
        ',firstImg,title1,subject_desc,left_line,img1,play,share' : 
          'firstImg,title1,subject_desc,left_line,img1,play,share');*/
};

