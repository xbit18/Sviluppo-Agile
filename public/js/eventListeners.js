
/* Music Pause */ 

var channel = Echo.channel('my-party');
channel.listen('.music.paused', () => {
    console.log('evento triggered')
})

/* ----------  */