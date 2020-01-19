# CHINESE AUDIO LESSONS GENERATOR

Collection of scripts meant to help learning a language. It is written in PHP. Runs as development php built in command line server.

## Requirements

To generate the audio the script [gTTS](https://github.com/pndurette/gTTS) is used. Thus to generate audios gTTS must be installed in your system:

```bash
  # pip package manager is needed to install gTTS
  sudo apt install python-pip

  # install gTTS
  pip install gTTS
```

## Starting web server

To start the server use:

```bash
  ./runserver
```

Open the link http://localhost:9906 in your browser.

## Configuration

In config.ini file the port number the server runs on may be edited.

## Generating lessons from command line

To create a lesson:

```bash
  # Create and open english version file for editing
  ./lesson <lesson_name>

  # Open chinese text file for editing
  ./zh-cn <lesson_name>

  # Generate audio files
  ./audio
```
