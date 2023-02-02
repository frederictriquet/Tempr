curl -s --user 'api:key-99125d6b68c3a6cbe2355172f5ffd002' \
  https://api.mailgun.net/v3/tempr.me/messages \
  -F from='TempR <no-reply@tempr.me>' \
  -F to=web-hPQdFI@mail-tester.com \
  -F to=frederic.triquet@gmail.com \
  -F subject='New Experiment' \
  -F text='This is awesome!'
