<template>
	<div>
		<div class="modal-mask" v-if="show" transition="modal" @click="requestClose && requestClose()">
    <div class="modal-wrapper">
      <div class="modal-container" @click.stop :style="{width: width + 'px'}">
				<slot></slot>
      </div>
    </div>
  </div>
</div>
</template>
<style scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, .8);
  display: block;
  transition: opacity .3s ease;
	overflow: auto;
	padding-top: 100px;
}

.modal-wrapper {
  display: block;
  vertical-align: middle;
}

.modal-container {
  width: 500px;
  margin: 0px auto;
  padding: 0;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
  transition: all .3s ease;
}

/*
 * the following styles are auto-applied to elements with
 * v-transition="modal" when their visiblity is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter, .modal-leave {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
<script>
export default {
	name: 'Modal',
	watch: {
		show (show) {
			if (show) {
				document.body.parentElement.classList.add('noscroll');
			}
			else {
				document.body.parentElement.classList.remove('noscroll');
			}
		}
	},
	props: {
		show: {
			type: Boolean
		},
		title: {
			type: String
		},
		requestClose: {
			type: Function
		},
		width: {
			type: String
		}
	}
};
</script>
