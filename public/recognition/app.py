import dlib
from flask import Flask, request, jsonify
from flask_cors import CORS
import cv2
import numpy as np
import base64
import os

app = Flask(__name__)
CORS(app, supports_credentials=True) 

# Use frontal face detector of Dlib
detector = dlib.get_frontal_face_detector()

class Face_Register:
    def __init__(self):
        self.current_frame_faces_cnt = 0  #  cnt for counting faces in current frame
        self.existing_faces_cnt = 0  # cnt for counting saved faces
        self.ss_cnt = 0  #  cnt for screen shots

    def base64_to_image_frame(self,image_data):
        image = cv2.imdecode(np.frombuffer(base64.b64decode(image_data.split(',')[1]), np.uint8), 1)
        resized_image = cv2.resize(image, (640, 480))
        return cv2.cvtColor(resized_image, cv2.COLOR_BGR2RGB)
    
    def face_capturing(self,faces,current_frame,save_path):
        save_path = "/Users/tuan.phan/Desktop/Yeez/public/recognition/data/data_faces_from_camera/" + save_path
        if len(faces) != 0:
            #   Show the ROI of faces
            for k, d in enumerate(faces):
                self.face_ROI_width_start = d.left()
                self.face_ROI_height_start = d.top()
                #  Compute the size of rectangle box
                self.face_ROI_height = (d.bottom() - d.top())
                self.face_ROI_width = (d.right() - d.left())
                self.hh = int(self.face_ROI_height / 2)
                self.ww = int(self.face_ROI_width / 2)

                # If the size of ROI > 480x640
                if (d.right() + self.ww) > 640 or (d.bottom() + self.hh > 480) or (d.left() - self.ww < 0) or (
                        d.top() - self.hh < 0):
                    
                    return jsonify({'message': 'Out of range'}, 200)
                    
                else:
                    files = os.listdir(save_path)
                    files = [file for file in files if os.path.isfile(os.path.join(save_path, file))]
                    file_count = len(files)
     
                    self.face_ROI_image = np.zeros((int(self.face_ROI_height * 2), self.face_ROI_width * 2, 3),
                                                   np.uint8)
                    for ii in range(self.face_ROI_height * 2):
                        for jj in range(self.face_ROI_width * 2):
                            self.face_ROI_image[ii][jj] = current_frame[self.face_ROI_height_start - self.hh + ii][
                                self.face_ROI_width_start - self.ww + jj]
            
                    cv2.imwrite(save_path + "/img_face_" + str(file_count + 1) + ".jpg", cv2.cvtColor(self.face_ROI_image, cv2.COLOR_BGR2RGB))
                    return jsonify({'message': 'Image received and processed successfully'}, 200)
            
        return jsonify({'message': 'No face detected'}, 200)

Face_Register_con = Face_Register()

@app.route('/api/upload', methods=['POST'])
def upload_image():
    try:
        data = request.json 
        image_data = data.get('image', '')
        save_path = data.get('savePath', '')
        print(save_path)

        current_frame = Face_Register_con.base64_to_image_frame(image_data)
        faces = detector(current_frame, 0)
        return Face_Register_con.face_capturing(faces, current_frame, save_path)
    
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    ssl_certificate_path = '/Users/tuan.phan/Desktop/Yeez/ssl_configurations/certs/ssl.pem'
    ssl_key_path = '/Users/tuan.phan/Desktop/Yeez/ssl_configurations/certs/ssl-key.pem'

    # Run the app with SSL
    app.run(ssl_context=(ssl_certificate_path, ssl_key_path), debug=True)
    # app.run(debug=True)
