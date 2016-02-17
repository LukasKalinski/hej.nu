/**
* void preloadImages(string, string)
* Preloads images into browser cache.
*
* @param string imgDir      The directory of the images.
* @param string strImages   Images separated with pipe (|).
*/
var LIB_IMAGE = true;
var PRELOADED_IMAGES = new Array();
var PRELOADED_IMAGES_KEY = 0;
function preloadImages(imgDir, strImages)
{
  var images = strImages.split('|');
  for(var i=0; i<images.length; i++)
  {
    PRELOADED_IMAGES[PRELOADED_IMAGES_KEY] = new Image();
    PRELOADED_IMAGES[PRELOADED_IMAGES_KEY].src = imgDir+'/'+images[i];
    PRELOADED_IMAGES_KEY++;
  }
}