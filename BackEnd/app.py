from flask import Flask, render_template, url_for, request, session, redirect
import numpy as np
import tensorflow as tf
from tensorflow.keras import layers , activations , models , preprocessing
from tensorflow.keras import preprocessing , utils
import yaml

questions = list()
answers = list()

stream = open('model/convo.yml' , 'rb')
docs = yaml.safe_load(stream)
conversations = docs['conversations']
for con in conversations:
  if len( con ) > 2 :
    questions.append(con[0])
    replies = con[1 :]
    ans = ''
    for rep in replies:
      ans += ' ' + rep
      answers.append(ans)
  elif len( con )> 1:
    questions.append(con[0])
    answers.append(con[1])

answers_with_tags = list()
for i in range( len( answers ) ):
    if type( answers[i] ) == str:
        answers_with_tags.append( answers[i] )
    else:
        questions.pop( i )

answers = list()
for i in range( len( answers_with_tags ) ) :
    answers.append( '<START> ' + answers_with_tags[i])

tokenizer = preprocessing.text.Tokenizer()
tokenizer.fit_on_texts( questions + answers )
VOCAB_SIZE = len( tokenizer.word_index )+1
#print( 'VOCAB SIZE : {}'.format( VOCAB_SIZE ))

# encoder_input_data
tokenized_questions = tokenizer.texts_to_sequences( questions )
maxlen_questions = max( [ len(x) for x in tokenized_questions ] )
padded_questions = preprocessing.sequence.pad_sequences( tokenized_questions , maxlen=maxlen_questions , padding='post' )
encoder_input_data = np.array( padded_questions )
#print( encoder_input_data.shape , maxlen_questions )

# decoder_input_data
tokenized_answers = tokenizer.texts_to_sequences( answers )
maxlen_answers = max( [ len(x) for x in tokenized_answers ] )
padded_answers = preprocessing.sequence.pad_sequences( tokenized_answers , maxlen=maxlen_answers , padding='post' )
decoder_input_data = np.array( padded_answers )
#print( decoder_input_data.shape , maxlen_answers )

# decoder_output_data
tokenized_answers = tokenizer.texts_to_sequences( answers )
for i in range(len(tokenized_answers)) :
    tokenized_answers[i] = tokenized_answers[i][1:]
padded_answers = preprocessing.sequence.pad_sequences( tokenized_answers , maxlen=maxlen_answers , padding='post' )
onehot_answers = utils.to_categorical( padded_answers , VOCAB_SIZE )
decoder_output_data = np.array( onehot_answers )
#print( decoder_output_data.shape )

	
def str_to_tokens(sentence : str ):
    words = sentence.lower().split()
    tokens_list = list()
    for word in words:
      tokens_list.append(tokenizer.word_index[word]) 
    return preprocessing.sequence.pad_sequences( [tokens_list] , maxlen=maxlen_questions , padding='post')

enc_model = models.load_model('enc_model.h5')
dec_model = models.load_model('dec_model.h5')


app = Flask(__name__)


@app.route('/')
def index():
	return render_template("index.html")
	
@app.route('/login', methods = ['GET', 'POST'])
def login():

	error = None
	if request.method == 'POST':
		if request.form['email'] != 'admin' or request.form['password'] != 'admin':
			error = 'Invalid Credentials. Try again.'
		else:
			return redirect(url_for('index'))
	return render_template("login.html", error = error)


@app.route('/register', methods = ['GET', 'POST'])
def register():
	msg = ''
	if request.method == 'POST' and 'username' in request.form and 'password1' in request.form and 'confirm_password' in request.form and 'email' in request.form:
		username = request.form['username']
		password = request.form['password']
		confirm_password = request.form['confirm_password']
		email = request.form['email']
	elif request.method == 'POST':
		msg = 'please fill out the form!'
		
	return render_template("register.html", msg = msg)
	
@app.route('/botpage')
def botpage():
	return render_template('botpage.html')
	
@app.route("/get")
def chatbot_predictioin():
	userText = request.args.get('msg')
	states_values = enc_model.predict(str_to_tokens(userText))
	empty_target_seq = np.zeros(( 1 , 1 ))
	empty_target_seq[0, 0] = tokenizer.word_index['start']
	stop_condition = False
	decoded_translation = ''
	while not stop_condition :
		dec_outputs , h , c = dec_model.predict([ empty_target_seq ] + states_values)
		sampled_word_index = np.argmax(dec_outputs[0, -1, :])
		sampled_word = None
		for word , index in tokenizer.word_index.items():
			if sampled_word_index == index :
				decoded_translation += ' {}'.format(word)
				sampled_word = word
        
		if sampled_word == 'end' or len(decoded_translation.split()) > maxlen_answers:
			stop_condition = True
            
		empty_target_seq = np.zeros((1 , 1))  
		empty_target_seq[0 , 0] = sampled_word_index
		states_values = [h , c]
	return decoded_translation

if __name__ == "__main__":
	app.run(debug=True)