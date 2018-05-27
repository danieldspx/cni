$(document).ready(function(){
    toastr.options.progressBar = true;
    toastr.options.closeButton = true;
    toastr.options.preventDuplicates = true;
});

function pushMessage(msg,callback){
    message = JSON.parse(msg);
    if(message.hasOwnProperty('type') && message.hasOwnProperty('text')){
        switch (message.type) {
            case 'warning':
                toastr.warning(message.text,message.title);
                break;
            case 'success':
                toastr.success(message.text,message.title);
                break;
            case 'error':
                toastr.error(message.text,message.title);
                break;
            default:
                toastr.info(message.text,message.title);
        }    
    }
    if(callback && typeof callback === 'function'){
        callback(message.type);
    }
};
