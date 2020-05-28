$( document ).ready( function() {
  'use strict';



/** -------Prendo il codice del party dall'URI ------**/

  var party_code = window.location.href.slice(33);
  var channel = Echo.join(`party.${party_code}`);
  
  /* Music Pause */ 

  /**
   * Comunica a tutti i partecipanti del canale
   */
  channel.here((users) => {
    console.log(users)
  });

  /**
   *  Action a utente entrante
   */
  channel.joining((user) => {
    console.log('joining')
    console.log(user)
  })

  /**
   *  Comunica a tutti che un utente lascia il canale
   */
  channel.leaving((user) => {
    console.log('leaving')
    console.log(user)
  })

  /* Music Pause */

  /**
   * Per i partecipanti : ascolta l'evento paused
   */
  channel.listen('.player.paused', () => {
    console.log('player paused')
  })
  
  
  

})