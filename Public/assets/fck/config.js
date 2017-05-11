/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
        config.forcePasteAsPlainText =true;
        config.pasteFromWordRemoveStyle = true;  
	config.extraPlugins += (config.extraPlugins ? ',img,title,bofang,down,share,describe,wordformat,wordformatto,css' : 'bofang,img,down,title,share,describe,wordformat,wordformatto,css');
};
