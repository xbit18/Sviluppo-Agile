$( document ).ready( function() {
  'use strict';



/** -------Prendo il codice del party dall'URI ------**/

var party_code = window.location.href.slice(33);

  /* Music Pause */ 

  var channel = Echo.channel(`party.${party_code}`);
  channel.listen('.music.paused', () => {
      console.log('music paused')
  })
  
  /* ----------  */

})