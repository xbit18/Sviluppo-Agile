$( document ).ready( function() {
  'use strict';



/** -------Prendo il codice del party dall'URI ------**/

  var party_code = window.location.href.slice(33);
  var channel = Echo.join(`party.${party_code}`);
  
  /* Music Pause */ 
  channel.here((users) => {
    console.log(users)
  });
  channel.joining((user) => {
    console.log('joining')
    console.log(user)
  })
  channel.leaving((user) => {
    console.log('leaving')
    console.log(user)
  })

  /* Music Pause */

  channel.listen('.music.paused', () => {
    console.log('music paused')
  })
  
  /* Music Play */
  channel.listen('.music.played', (/** Informazione sul brano**/) => {
    console.log('music played')
  })

})