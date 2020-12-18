<template>
<nav class="modal-navigation">
    <slot></slot>    
</nav>
</template>
<style scoped>
.modal-navigation{
  display: flex;
  border-bottom: 1px solid #A7DAD3;
	margin: 0 25px;
	}


.modal-navigation a{

  margin-right: -8px;
line-height: 1.42857143;
border: 1px solid transparent;
border-radius: 4px 4px 0 0;
margin-top: 30px;
letter-spacing: 1px;
font-size: 16px;

padding: 10px 15px;

}

.modal-navigation a.active,
.modal-navigation a:hover{
  cursor: pointer;

  color: #F26841;
cursor: default;
background-color: #fff;
border: 1px solid #A7DAD3;
border-bottom-color: transparent;
margin-top: 30px;
letter-spacing: 1px;
}

</style>