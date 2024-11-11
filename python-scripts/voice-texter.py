import speech_recognition as sr
import os
from pydub import AudioSegment
from pydub.silence import split_on_silence
import sys


def transcribe_large_audio(path):

    r = sr.Recognizer()
    
    # Load the audio file
    sound = AudioSegment.from_wav(path)
    
    # Split audio where silence is 700ms or more and get chunks
    chunks = split_on_silence(sound, min_silence_len=700, silence_thresh=sound.dBFS-14, keep_silence=700)
    
    # Create a directory to store the audio chunks
    folder_name = "voices"
    if not os.path.isdir(folder_name):
        os.mkdir(folder_name)
    
    whole_text = ""
    
    # Process each chunk
    for i, audio_chunk in enumerate(chunks, start=1):
        # Export audio chunk and save it in the `folder_name` directory
        chunk_filename = os.path.join(folder_name, f"chunk{i}.wav")
        audio_chunk.export(chunk_filename, format="wav")
        
        # Recognize the chunk
        with sr.AudioFile(chunk_filename) as source:
            audio_listened = r.record(source)
            try:
                text = r.recognize_google(audio_listened)
            except sr.UnknownValueError as e:
                print("Error:", str(e))
            else:
                text = f"{text.capitalize()}. "
                whole_text += text
    
    return whole_text


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python voice-texter.py <audio_file_path>")
    else:
        file_path = sys.argv[1]
        result = transcribe_large_audio(file_path)
        print(result)
        