import dlib
import numpy as np
import cv2
import os
import pandas as pd
import time
import logging
import sqlite3
import datetime
from flask import Flask, request, render_template, Response, jsonify
from flask_cors import CORS
import threading
import base64
import csv
from imutils import face_utils
from scipy.spatial import distance

app = Flask(__name__)
CORS(app)

# Dlib  / Use frontal face detector of Dlib
detector = dlib.get_frontal_face_detector()

# Dlib landmark / Get face landmarks
predictor = dlib.shape_predictor('data/data_dlib/shape_predictor_68_face_landmarks.dat')

# Dlib Resnet Use Dlib resnet50 model to get 128D face descriptor
face_reco_model = dlib.face_recognition_model_v1("data/data_dlib/dlib_face_recognition_resnet_model_v1.dat")

# Create a connection to the database
conn = sqlite3.connect("attendance.db")
cursor = conn.cursor()

# Create a table for the current date
current_date = datetime.datetime.now().strftime("%Y_%m_%d")  # Replace hyphens with underscores
table_name = "attendance" 
create_table_sql = f"CREATE TABLE IF NOT EXISTS {table_name} (name TEXT, time TEXT, date DATE, UNIQUE(name, date))"
cursor.execute(create_table_sql)


# Commit changes and close the connection
conn.commit()
conn.close()

#  Path of cropped faces
path_images_from_camera = "data/data_faces_from_camera/"

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

class Face_Recognizer:
    def __init__(self):
        self.font = cv2.FONT_ITALIC

        # FPS
        self.frame_time = 0
        self.frame_start_time = 0
        self.fps = 0
        self.fps_show = 0
        self.start_time = time.time()

        # cnt for frame
        self.frame_cnt = 0

        #  Save the features of faces in the database
        self.face_features_known_list = []
        # / Save the name of faces in the database
        self.face_name_known_list = []

        #  List to save centroid positions of ROI in frame N-1 and N
        self.last_frame_face_centroid_list = []
        self.current_frame_face_centroid_list = []

        # List to save names of objects in frame N-1 and N
        self.last_frame_face_name_list = []
        self.current_frame_face_name_list = []

        #  cnt for faces in frame N-1 and N
        self.last_frame_face_cnt = 0
        self.current_frame_face_cnt = 0

        # Save the e-distance for faceX when recognizing
        self.current_frame_face_X_e_distance_list = []

        # Save the positions and names of current faces captured
        self.current_frame_face_position_list = []
        #  Save the features of people in current frame
        self.current_frame_face_feature_list = []

        # e distance between centroid of ROI in last and current frame
        self.last_current_frame_centroid_e_distance = 0

        #  Reclassify after 'reclassify_interval' frames
        self.reclassify_interval_cnt = 0
        self.reclassify_interval = 10

        self.current_modified_frame = None

    #  "features_all.csv"  / Get known faces from "features_all.csv"
    def get_face_database(self):
        if os.path.exists("data/features_all.csv"):
            path_features_known_csv = "data/features_all.csv"
            csv_rd = pd.read_csv(path_features_known_csv, header=None)
            for i in range(csv_rd.shape[0]):
                features_someone_arr = []
                self.face_name_known_list.append(csv_rd.iloc[i][0])
                for j in range(1, 129):
                    if csv_rd.iloc[i][j] == '':
                        features_someone_arr.append('0')
                    else:
                        features_someone_arr.append(csv_rd.iloc[i][j])
                self.face_features_known_list.append(features_someone_arr)
            logging.info("Faces in Database： %d", len(self.face_features_known_list))
            return 1
        else:
            logging.warning("'features_all.csv' not found!")
            logging.warning("Please run 'get_faces_from_camera.py' "
                            "and 'features_extraction_to_csv.py' before 'face_reco_from_camera.py'")
            return 0

    def update_fps(self):
        now = time.time()
        # Refresh fps per second
        if str(self.start_time).split(".")[0] != str(now).split(".")[0]:
            self.fps_show = self.fps
        self.start_time = now
        self.frame_time = now - self.frame_start_time
        self.fps = 1.0 / self.frame_time
        self.frame_start_time = now

    @staticmethod
    # / Compute the e-distance between two 128D features
    def return_euclidean_distance(feature_1, feature_2):
        feature_1 = np.array(feature_1)
        feature_2 = np.array(feature_2)
        dist = np.sqrt(np.sum(np.square(feature_1 - feature_2)))
        return dist

    # / Use centroid tracker to link face_x in current frame with person_x in last frame
    def centroid_tracker(self):
        for i in range(len(self.current_frame_face_centroid_list)):
            e_distance_current_frame_person_x_list = []
            #  For object 1 in current_frame, compute e-distance with object 1/2/3/4/... in last frame
            for j in range(len(self.last_frame_face_centroid_list)):
                self.last_current_frame_centroid_e_distance = self.return_euclidean_distance(
                    self.current_frame_face_centroid_list[i], self.last_frame_face_centroid_list[j])

                e_distance_current_frame_person_x_list.append(
                    self.last_current_frame_centroid_e_distance)

            last_frame_num = e_distance_current_frame_person_x_list.index(
                min(e_distance_current_frame_person_x_list))
            self.current_frame_face_name_list[i] = self.last_frame_face_name_list[last_frame_num]

    #  cv2 window / putText on cv2 window
    def draw_note(self, img_rd):
        #  / Add some info on windows
        cv2.putText(img_rd, "Face Recognizer with Deep Learning", (20, 40), self.font, 1, (255, 255, 255), 1, cv2.LINE_AA)
        cv2.putText(img_rd, "Frame:  " + str(self.frame_cnt), (20, 100), self.font, 0.8, (0, 255, 0), 1,
                    cv2.LINE_AA)
        cv2.putText(img_rd, "FPS:    " + str(self.fps.__round__(2)), (20, 130), self.font, 0.8, (0, 255, 0), 1,
                    cv2.LINE_AA)
        cv2.putText(img_rd, "Faces:  " + str(self.current_frame_face_cnt), (20, 160), self.font, 0.8, (0, 255, 0), 1,
                    cv2.LINE_AA)
        cv2.putText(img_rd, "Q: Quit", (20, 450), self.font, 0.8, (255, 255, 255), 1, cv2.LINE_AA)

        for i in range(len(self.current_frame_face_name_list)):
            img_rd = cv2.putText(img_rd, "Face_" + str(i + 1), tuple(
                [int(self.current_frame_face_centroid_list[i][0]), int(self.current_frame_face_centroid_list[i][1])]),
                                 self.font,
                                 0.8, (255, 190, 0),
                                 1,
                                 cv2.LINE_AA)
    # insert data in database

    def attendance(self, name):
        current_date = datetime.datetime.now().strftime('%Y-%m-%d')
        conn = sqlite3.connect("attendance.db")
        cursor = conn.cursor()
        # Check if the name already has an entry for the current date
        cursor.execute("SELECT * FROM attendance WHERE name = ? AND date = ?", (name, current_date))
        existing_entry = cursor.fetchone()

        if existing_entry:
            print(f"{name} is already marked as present for {current_date}")
        else:
            current_time = datetime.datetime.now().strftime('%H:%M:%S')
            cursor.execute("INSERT INTO attendance (name, time, date) VALUES (?, ?, ?)", (name, current_time, current_date))
            conn.commit()
            print(f"{name} marked as present for {current_date} at {current_time}")

        conn.close()

    #  Face detection and recognition wit OT from input video stream
    def process(self, stream):
        # 1.  Get faces known from "features.all.csv"
        if self.get_face_database():
            while stream.isOpened():
                self.frame_cnt += 1
                logging.debug("Frame " + str(self.frame_cnt) + " starts")
                flag, img_rd = stream.read()
                kk = cv2.waitKey(1)

                # 2.  Detect faces for frame X
                faces = detector(img_rd, 0)

                # 3.  Update cnt for faces in frames
                self.last_frame_face_cnt = self.current_frame_face_cnt
                self.current_frame_face_cnt = len(faces)

                # 4.  Update the face name list in last frame
                self.last_frame_face_name_list = self.current_frame_face_name_list[:]

                # 5.  update frame centroid list
                self.last_frame_face_centroid_list = self.current_frame_face_centroid_list
                self.current_frame_face_centroid_list = []

                # 6.1  if cnt not changes
                if (self.current_frame_face_cnt == self.last_frame_face_cnt) and (
                        self.reclassify_interval_cnt != self.reclassify_interval):
                    logging.debug("scene 1:   No face cnt changes in this frame!!!")

                    self.current_frame_face_position_list = []

                    if "unknown" in self.current_frame_face_name_list:
                        self.reclassify_interval_cnt += 1

                    if self.current_frame_face_cnt != 0:
                        for k, d in enumerate(faces):
                            self.current_frame_face_position_list.append(tuple(
                                [faces[k].left(), int(faces[k].bottom() + (faces[k].bottom() - faces[k].top()) / 4)]))
                            self.current_frame_face_centroid_list.append(
                                [int(faces[k].left() + faces[k].right()) / 2,
                                 int(faces[k].top() + faces[k].bottom()) / 2])

                            img_rd = cv2.rectangle(img_rd,
                                                   tuple([d.left(), d.top()]),
                                                   tuple([d.right(), d.bottom()]),
                                                   (255, 255, 255), 2)

                    #  Multi-faces in current frame, use centroid-tracker to track
                    if self.current_frame_face_cnt != 1:
                        self.centroid_tracker()

                    for i in range(self.current_frame_face_cnt):
                        # 6.2 Write names under ROI
                        img_rd = cv2.putText(img_rd, self.current_frame_face_name_list[i],
                                             self.current_frame_face_position_list[i], self.font, 0.8, (0, 255, 255), 1,
                                             cv2.LINE_AA)
                    self.draw_note(img_rd)

                # 6.2  If cnt of faces changes, 0->1 or 1->0 or ...
                else:
                    logging.debug("scene 2: / Faces cnt changes in this frame")
                    self.current_frame_face_position_list = []
                    self.current_frame_face_X_e_distance_list = []
                    self.current_frame_face_feature_list = []
                    self.reclassify_interval_cnt = 0

                    # 6.2.1  Face cnt decreases: 1->0, 2->1, ...
                    if self.current_frame_face_cnt == 0:
                        logging.debug("  / No faces in this frame!!!")
                        # clear list of names and features
                        self.current_frame_face_name_list = []
                    # 6.2.2 / Face cnt increase: 0->1, 0->2, ..., 1->2, ...
                    else:
                        logging.debug("  scene 2.2  Get faces in this frame and do face recognition")
                        self.current_frame_face_name_list = []
                        for i in range(len(faces)):
                            shape = predictor(img_rd, faces[i])
                            self.current_frame_face_feature_list.append(
                                face_reco_model.compute_face_descriptor(img_rd, shape))
                            self.current_frame_face_name_list.append("unknown")

                        # 6.2.2.1 Traversal all the faces in the database
                        for k in range(len(faces)):
                            logging.debug("  For face %d in current frame:", k + 1)
                            self.current_frame_face_centroid_list.append(
                                [int(faces[k].left() + faces[k].right()) / 2,
                                 int(faces[k].top() + faces[k].bottom()) / 2])

                            self.current_frame_face_X_e_distance_list = []

                            # 6.2.2.2  Positions of faces captured
                            self.current_frame_face_position_list.append(tuple(
                                [faces[k].left(), int(faces[k].bottom() + (faces[k].bottom() - faces[k].top()) / 4)]))

                            # 6.2.2.3 
                            # For every faces detected, compare the faces in the database
                            for i in range(len(self.face_features_known_list)):
                                # 
                                if str(self.face_features_known_list[i][0]) != '0.0':
                                    e_distance_tmp = self.return_euclidean_distance(
                                        self.current_frame_face_feature_list[k],
                                        self.face_features_known_list[i])
                                    logging.debug("      with person %d, the e-distance: %f", i + 1, e_distance_tmp)
                                    self.current_frame_face_X_e_distance_list.append(e_distance_tmp)
                                else:
                                    #  person_X
                                    self.current_frame_face_X_e_distance_list.append(999999999)

                            # 6.2.2.4 / Find the one with minimum e distance
                            similar_person_num = self.current_frame_face_X_e_distance_list.index(
                                min(self.current_frame_face_X_e_distance_list))

                            if min(self.current_frame_face_X_e_distance_list) < 0.4:
                                self.current_frame_face_name_list[k] = self.face_name_known_list[similar_person_num]
                                logging.debug("  Face recognition result: %s",
                                              self.face_name_known_list[similar_person_num])
                                
                                # Insert attendance record
                                nam =self.face_name_known_list[similar_person_num]

                                print(type(self.face_name_known_list[similar_person_num]))
                                print(nam)
                                self.attendance(nam)
                            else:
                                logging.debug("  Face recognition result: Unknown person")

                        # 7.  / Add note on cv2 window
                        self.draw_note(img_rd)

                # 8.  'q'  / Press 'q' to exit
                if kk == ord('q'):
                    break

                self.update_fps()
                # cv2.namedWindow("camera", 1)
                # cv2.imshow("camera", img_rd)
                self.current_modified_frame = img_rd

                logging.debug("Frame ends\n\n")

    def get_frames(self):
        return self.current_modified_frame

    def run(self):
        # cap = cv2.VideoCapture("video.mp4")  # Get video stream from video file
        cap = cv2.VideoCapture(0)              # Get video stream from camera
        self.process(cap)

        cap.release()
        cv2.destroyAllWindows()

Face_Recognizer_con = Face_Recognizer()
Face_Register_con = Face_Register()

def run_flask():
    app.run(debug=True)

def run_opencv():
    Face_Recognizer_con.run()

def generate_frames():
    while True:
        frame = Face_Recognizer_con.get_frames()
        _, buffer = cv2.imencode('.jpg', frame)
        img_encoded = buffer.tobytes()
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + img_encoded + b'\r\n')
        

#  Return 128D features for single image
def return_128d_features(path_img):
    img_rd = cv2.imread(path_img)
    faces = detector(img_rd, 1)

    logging.info("%-40s %-20s", " Image with faces detected:", path_img)

    # For photos of faces saved, we need to make sure that we can detect faces from the cropped images
    if len(faces) != 0:
        shape = predictor(img_rd, faces[0])
        face_descriptor = face_reco_model.compute_face_descriptor(img_rd, shape)
    else:
        face_descriptor = 0
        logging.warning("no face")
    return face_descriptor


#   Return the mean value of 128D face descriptor for person X
def return_features_mean_personX(path_face_personX):
    features_list_personX = []
    photos_list = os.listdir(path_face_personX)
    if photos_list:
        for i in range(len(photos_list)):
            #  return_128d_features()  128D  / Get 128D features for single image of personX
            logging.info("%-40s %-20s", " / Reading image:", path_face_personX + "/" + photos_list[i])
            features_128d = return_128d_features(path_face_personX + "/" + photos_list[i])
            #  Jump if no face detected from image
            if features_128d == 0:
                i += 1
            else:
                features_list_personX.append(features_128d)
    else:
        logging.warning(" Warning: No images in%s/", path_face_personX)

   
    if features_list_personX:
        features_mean_personX = np.array(features_list_personX, dtype=object).mean(axis=0)
    else:
        features_mean_personX = np.zeros(128, dtype=object, order='C')
    return features_mean_personX

def save_feature_to_csv():
    person_list = os.listdir("data/data_faces_from_camera/")
    person_list.sort()

    with open("data/features_all.csv", "w", newline="") as csvfile:
        writer = csv.writer(csvfile)
        for person in person_list:
            # Get the mean/average features of face/personX, it will be a list with a length of 128D
            logging.info("%sperson_%s", path_images_from_camera, person)
            features_mean_personX = return_features_mean_personX(path_images_from_camera + person)

            if len(person.split('_', 2)) == 2:
                # "person_x"
                person_name = person
            else:
                # "person_x_tom"
                person_name = person.split('_', 2)[-1]
            features_mean_personX = np.insert(features_mean_personX, 0, person_name, axis=0)
            # features_mean_personX will be 129D, person name + 128 features
            writer.writerow(features_mean_personX)
            logging.info('\n')
        logging.info("Save all the features of faces registered into: data/features_all.csv")


def eye_aspect_ratio(eye):
	A = distance.euclidean(eye[1], eye[5])
	B = distance.euclidean(eye[2], eye[4])
	C = distance.euclidean(eye[0], eye[3])
	ear = (A + B) / (2.0 * C)
	return ear

(lStart, lEnd) = face_utils.FACIAL_LANDMARKS_68_IDXS["left_eye"]
(rStart, rEnd) = face_utils.FACIAL_LANDMARKS_68_IDXS["right_eye"]

thresh = 0.25
frame_check = 20

@app.route('/drowsiness_detection', methods=['POST'])
def drowsiness_detection():
    try:
        flag=0
        data = request.json
        image_data = data.get('image', '')
        current_frame = Face_Register_con.base64_to_image_frame(image_data)
        subjects = detector(current_frame, 0)
        for subject in subjects:
            shape = predictor(current_frame, subject)

            shape = face_utils.shape_to_np(shape)#converting to NumPy Array
            leftEye = shape[lStart:lEnd]
            rightEye = shape[rStart:rEnd]
            leftEAR = eye_aspect_ratio(leftEye)
            rightEAR = eye_aspect_ratio(rightEye)
            ear = (leftEAR + rightEAR) / 2.0
     
            # leftEyeHull = cv2.convexHull(leftEye)
            # rightEyeHull = cv2.convexHull(rightEye)
            # cv2.drawContours(frame, [leftEyeHull], -1, (0, 255, 0), 1)
            # cv2.drawContours(frame, [rightEyeHull], -1, (0, 255, 0), 1)
            if ear < thresh:
                flag += 1
                if flag >= frame_check:
                    # cv2.putText(frame, "****************DISTRACTED!****************", (10,325),
                    #     cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 0, 255), 2)

                    return jsonify({'message': 'Drowsy'}, 200)
            else:
                # cv2.putText(frame, "****************FOCUSING!****************", (10, 325),
                #             cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 255, 0), 2)
                return jsonify({'message': 'Focusing'}, 200)
                flag = 0

        return jsonify({'message': 'No Face Detected'}, 200)

    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/features_extraction')
def features_extraction():
    save_feature_to_csv()

@app.route('/attendance_taker')
def attendance_taker():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/face_capturing')
def face_capturing():
    try:
        data = request.json 
        image_data = data.get('image', '')
        save_path = data.get('savePath', '')

        current_frame = Face_Register_con.base64_to_image_frame(image_data)
        faces = detector(current_frame, 0)
        return Face_Register_con.face_capturing(faces, current_frame, save_path)
    
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    opencv_thread = threading.Thread(target=run_opencv)
    opencv_thread.start()
    app.run(debug=True)
    opencv_thread.join()
