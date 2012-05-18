Voice Comments for Box.net
==========================

What are we looking at here?
----------------------------
This is a prototype app I wrote for a [Twilio+Box contest](http://www.twilio.com/contests/2011/08/new-developer-contest-twilio-box.html) in August 2011. The app allows you to open your Box.net documents and attach voice comments to them.

Here's what you would do to use it:

1. Open the homepage and log in with your Box.net account.
2. Select one of your files.
3. Select some text that you want to comment on.
4. Press the "Record" button, (you'll have to accept Flash access to the microphone if it's the first time) and wait for the beep.
5. Say what you have to say and press "Stop" when done.
6. Repeat as necessary.
7. Reload the page: all the parts you've selected and commented on will be highlighted with an audio player in the margin.
8. Open the file on Box.net and note that a transcription of what you said has been posted as a comment to the file with a link to the app.
9. That link would open the app on the document and play back the audio comment.

Check this screencast out for a demo:
[http://www.youtube.com/watch?v=LA6yXWSwFZQ](http://www.youtube.com/watch?v=LA6yXWSwFZQ)

Why is the code so badly organized?
-----------------------------------
Well, that's not nice to say that, but you're right. Keep in mind that this is a prototype built for the unique purpose of the contest. Since it worked in the context of the demo, I'm fine with it.


Why are you sharing it if you think it's bad?
---------------------------------------------
Maybe somebody will find something useful in it. Or later, when I'm a superstar developer, I'll be able to point at this repo and say to the youngens "see how far I've come? You can too!".

License
-------
As usual, this is licensed under the MIT license, because really, do whatever you want with it and don't come back to me if it doesn't work.

Cheers,

Tim


PS: this repo doesn't have any commit history because I had some API keys in the code in there. It was just easier to clean everything and start from a clean history.