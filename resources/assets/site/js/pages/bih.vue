<template>

<div>
<header class="red">
	<h1 class="inner">Hey, Birthright Israel participants, BI alumni & BIH alumni...</h1>
</header>

<section class="big-image">
	<div class="inner">
		<h2>We bet you have some stories to tell.</h2>
		<img class="logo-bih" src="https://res.cloudinary.com/jaketv/image/upload/c_scale,f_auto,w_160/v1492891097/bih/bihlogo.png" />
		<img class="logo-jake" src="http://res.cloudinary.com/jaketv/image/upload/c_scale,f_auto,w_160/v1492891097/bih/jakelogo.png" />
	</div>
</section>


<section class="main-bih">
	<div class="inner">


	<b><i>JakeTV</i></b> and <b><i>Bring Israel Home</i></b>  are looking for a few good stories to film and feature on the JakeTV app,
	FB page, and website, as well as the BIH website and social media. If you are a current or past
	participant of <b><i>Bring Israel Home</i></b>  and have a tale to tell about either your Israel trip, how <b><i>BIH</i></b> impacted your
	life, or any inspiring Jewish experience, let us know. Your story might be selected to be told through an
	awesome video produced by JakeTV. To get started, just follow three easy steps:

	<br />
	<br />
</div>

<div class="inner dt">

  <aside class="tc" style="width:200px">
		<h1 style="text-align:left; padding:0;">ABOUT JAKE</h1>
		<br />
		<p>JakeTV is the home of video made by the world’s best producers of Jewish video that celebrates
		the experi-ence of being Jewish in all its diversity.<p>
			<br />
		<p> Sorry, only a limited number of stories will be filmed — your sub-mission does not guar-antee we will film your story. JAKE TV and
		BIH maintain rights to use all materials submitted for promotional use.</p>
	</aside>

  <div class="tc main-form" >

		<span class="big-number">1 </span> Download our app on
			<a href="#">iTunes</a> or
				<a href="https://itunes.apple.com/us/app/jake-tv/id1063239686?ls=1&mt=8"><img class="img-icon" src="https://res.cloudinary.com/jaketv/image/upload/f_auto,q_auto/bih/icon-apple.jpg" /></a>

<a href="#">Google Play</a>
				<a href="https://play.google.com/store/apps/details?id=com.jaketv.jaketvapp"><img class="img-icon"  src="https://res.cloudinary.com/jaketv/image/upload/f_auto,q_auto/bih/icon-android.jpg" /></a>

				<br />

    <span class="big-number">2 </span> Tell us about yourself and a bit about your story. You can upload a file,
		links to pics, or even a short video convincing us why we should make a short film about your experience.
    <br /><br />

		<input v-model="entry.name" placeholder="Name" type="text" />
    <br />
		<input v-model="entry.email" placeholder="Email" type="email" />
    <br />
		<textarea rows="10" v-model="entry.story" placeholder="What’s the story?"></textarea>
<br />
		Attach a file, photo or video (optional)<br />
		<FileChooser class="file-upload" @fileChosen="upload"></FileChooser>
		<span v-if="uploadProgress !== null">Uploading {{ Math.round(uploadProgress * 100) }}%</span>


		<small>
		Accepted types: jpg, gif, png, pdf, jpeg, tiff, avi, mov, mp4.</small>
	<br /><br />
	<p>You can alternately submit your entry by FB message: Facebook.com/jaketv.tv or Whats App: 347-915-6177</p>

<br />
<br />



	<button class="button" @click.prevent="submit">Submit <i v-if="submitting" class="fa fa-spin fa-spinner"></i></button>

	<br />
	<br />

	  <span class="big-number">3 </span> Visit JakeTV on Facebook
		<a href="https://www.facebook.com/JakeTV.tv/"><img class="img-icon"  src="https://res.cloudinary.com/jaketv/image/upload/f_auto,q_auto/bih/icon-fb.jpg" /></a>
or the Web 		<a href="http://jaketv.tv/"><img class="img-icon" src="https://res.cloudinary.com/jaketv/image/upload/f_auto,q_auto/bih/icon-comp.jpg" /></a>



	</div>
</div>
</section>


<footer class="red">
	<h1 class="inner" >
		For more info, email us at <a href="mailto:jaketvchannel@gmail.com" >jaketvchannel@gmail.com</a>
	</h1>
</footer>
</div>

</template>
<script>
import axios from 'axios';
import Evaporate from 'evaporate';
import crypto from 'crypto';

const FileChooser = {
	name: 'FileChooser',
	render (h) {
		return h('input', {
			attrs: {
				type: 'file',
				value: 'Choose a File'
			},
			on: {
				change: e => this.$emit('fileChosen', e.currentTarget.files[0])
			}
		});
	}
};

export default {
	data () {
		return {
			entry: {
				name: null,
				email: null,
				story: null,
				file: null
			},
			submitting: false,
			uploading: false,
			uploadProgress: null
		};
	},
	created () {
		document.body.classList.add('bih');
	},
	beforeDestroy () {
		document.body.classList.remove('bih');
	},
	components: {
		FileChooser
	},
	methods: {
		upload (file) {
			Evaporate.create({
				bucket: 'bih-submissions',
				aws_key: 'AKIAIKTZZ6CLKBPTK6IQ',
				signerUrl: '/auth_upload',
				computeContentMd5: true,
				cryptoMd5Method (data) { return crypto.createHash('md5').update(data).digest('base64'); },
				cryptoHexEncodedHash256 (data) {
					return crypto.createHash('sha256').update(data).digest('hex');
				}
			}).then((evaporate) => {
				const addConfig = {
					name: `${new Date().getTime()} ${file.name}`,
					file,
					progress: (value) => { this.uploadProgress = value; },
					complete: () => { this.uploading = false; this.uploadProgress = null; }
				};

				evaporate.add(addConfig).then((key) => { this.entry.file = key; });
			});
		},
		submit () {
			this.submitting = true;

			axios({
				method: 'POST',
				url: 'submit_bih',
				data: this.entry
			}).then(() => { this.submitting = false; });
		}
	}
};
</script>

<style>
body.bih{
  background: #231f20;
  top: 0;
  height: 100vh;
}

</style>

<style scoped>


  .red{
    background: #f26a42;
  }

.big-image{
  background: url("https://res.cloudinary.com/jaketv/image/upload/c_scale,f_auto,w_1420/v1492891101/bih/bigimage.jpg");
  background-position: center;
  background-size: cover;
}

.big-image .inner{
  position: relative;
  height:600px;
}

.logo-bih{
  position: absolute;
  bottom: 10px;
  left: 0
}

.logo-jake{
  position: absolute;
  bottom: 10px;
  right: 0

}
.center-text{
  text-align: center;
  display: block;
}

.big-number{
	font-size: 32px;
	font-weight: bold;
}

h1{
  font-size: 16px;
  font-weight: bold;
  text-align: center;
  color: white;
  padding: 10px;
}

h2{
  padding-top: 20px;
  line-height: 100%;
  font-size: 50px;
  font-weight: bold;
  text-align: center;
}


.main-bih{
  color: #F5F4F3;
  padding-top: 10px;
}

.inner{
  width: 740px;
  margin: 0 auto;

}

.dt{
  display: table;
}

.tc{
  display: table-cell;
}

aside{
  border-right: 1px solid #fff;
  font-size: 14px;
  padding-right: 15px;
}
.main-form{
  padding-left: 40px;
	font-size: 14px;
}

footer{
    width: 100vw;
}


.button {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	line-height: 14px;
	color: #ffffff;
	padding: 10px 20px;
	background: -webkit-gradient(
		linear, left top, left bottom,
		from(#ffe057),
		color-stop(0.25, #ffd582),
		to(#faa046));
	border-radius: 15px;
	border: 3px solid #ffffff;
	box-shadow:
		0px 3px 3px rgba(099,077,061,0.5),
		inset 0px 0px 1px rgba(255,106,000,1);
	text-shadow:
		0px -1px 0px rgba(000,000,000,0.2),
		0px 1px 0px rgba(255,255,255,0.3);
}

.img-icon{
	position: relative;
	top: 20px;
}

.file-upload{
	font-size: 14px;
	height: 34px;
	border-radius: 0;
	color: #333;
	padding: 5px 5px;
	display: block;
	border: 0 none;
	border-bottom: 1px solid #ddd;
	width: 100%;
	outline: none;
	box-shadow: none;
	background: white;
}


</style>
