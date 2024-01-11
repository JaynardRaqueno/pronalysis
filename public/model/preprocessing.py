# preprocessing.py

import re
from nltk.tokenize import word_tokenize

def data(Comments):
    Comments = str(Comments).lower()
    Comments = re.sub("[^a-zA-Z0-9\s]", '', Comments)
    temp_final = []
    for word in Comments.split():
        if word == '' or '\r\n' in word:
            continue
        else:
            temp_final.append(word)
    processed_text = ' '.join(temp_final)
    return word_tokenize(processed_text)
