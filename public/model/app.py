from flask import Flask, request, jsonify, send_file
import pickle
from flask_cors import CORS
import sys
import os
import pandas as pd  # For reading CSV and Excel files
import io
import traceback  # Import traceback module

# Add the model directory to sys.path
sys.path.append('C:\\xampp\\htdocs\\pronalysis\\public\\model')

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Load the saved model
try:
    with open('C:\\xampp\\htdocs\\pronalysis\\public\\model\\text_clf_svm.pkl', 'rb') as file:
        loaded_model = pickle.load(file)
except Exception as e:
    print(f"Failed to load the model: {e}")
    sys.exit(1)

@app.route('/classify', methods=['POST'])
def predict():
    try:
        # Check if data is in JSON format
        if request.json and 'data' in request.json:
            data = request.json.get('data')
            prediction = loaded_model.predict([data])[0]
            return jsonify({'prediction': prediction})
        else:
            return jsonify({'error': 'Invalid request format'}), 400
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/start_server', methods=['GET'])
def start_server():
    try:
        # Start the Flask server as a background process
        os.system('nohup python pronalysis_flask_app.py ')
        return jsonify({'status': 'success'}), 200
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5000)  # Running the app on port 5000 in debug mode
