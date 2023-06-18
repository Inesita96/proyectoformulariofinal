function validate(evt){
  evt.preventDefault();
  document.getElementById("phpError").innerHTML = ''
  var submitSucceed = true;
  let inputIds = ['nombre', 'primerApellido', 'segundoApellido', 'email', 'password'];
  let phpbody =""
  inputIds.forEach(function(id){
    var inputElement = document.getElementById(id);
    var inputText = inputElement.value; 
    if(isEmpty(id)){
      printError(id, "Rellene este campo");
      submitSucceed = false;
    }else{
      var successInput = true
      switch(id) {
        case 'nombre':
          successInput = validateNombreApellidos(id, inputText);
          console.log(successInput)
          break;
        case 'primerApellido':
          successInput = validateNombreApellidos(id, inputText);
          break;
        case 'segundoApellido':
          successInput = validateNombreApellidos(id, inputText);
          break;
        case 'email':
          successInput = validateEmail(id, inputText);
          break;
        case 'password':
          successInput = validatePassword(id, inputText);
          break;
        default:
          break;
      };
      
      if(successInput){
        printSuccess(id)
        if(phpbody==""){
          phpbody+=`${id}=${inputText}`
        }else{
          phpbody+=`&${id}=${inputText}`
        } 
      }

      submitSucceed = successInput ? submitSucceed : successInput;
    }
  })

  if(submitSucceed){
    data =  crearUsuarioPhp(phpbody).then(data => {
      console.log(data);
      console.log(data.error);
      console.log(data.success);
      if(data.error != ''){
        document.getElementById("phpError").innerHTML = data.error
      }else{
        alert(data.success)
      }
    });
  }
  return false
}

function printError(id, msg){
  changeErrorMessage(id, msg)
  switchClasses(id, "success", "error")
}

function printSuccess(id){
  changeErrorMessage(id, '')
  switchClasses(id, "error", "success")
}

function isEmpty(id){
  return !document.getElementById(id).value
}

function switchClasses(id, toRemoveClass, toAddClass){
  let element = document.getElementById(id);

  element.classList.remove(toRemoveClass);
  element.classList.add(toAddClass);
}

function changeErrorMessage(id, msg){
  document.getElementById(id+'Error').textContent=msg;
}

function validateNombreApellidos(id, inputText){
  const regexNames = /^(?!([a-zA-Z]+(\s[a-zA-Z]+)*$))/gm
  if(regexNames.test(inputText)){
    printError(id, "Solo se admiten carácteres alfabéticos y un espacio entre palabras");
    return false
  }
  return true;
}

function validateEmail(id, inputText){
  const regexEmail = /^(([\w-\.]+@([\w-]+\.)+[\w-]{2,4}))$/gm
  if(!regexEmail.test(inputText)){
    printError(id, "Email Inválido");
    return false
  }
  return true;
}

function validatePassword(id, inputText){
  var passwordLength = inputText.length;
  if(passwordLength < 4 || passwordLength > 8){
    printError(id, "Debe tener entre 4 y 8 caracteres");
    return false
  }
  return true;
}

async function crearUsuarioPhp(phpbody){
  let response = await fetch('crearusuario.php', {method: 'POST',headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},body: phpbody});
  let data = await response.json();
  return data;
}
