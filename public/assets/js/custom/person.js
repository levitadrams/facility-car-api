$(document).ready(function () {

    $('#removerCapa').click(function () {
        $('#removeImage').val(1);
    });

});


// IMAGEM
var cropper;
var currentInputId;
var originalFileName; // Armazena o nome original do arquivo
var carregar_crop = true;

function button_to_click_input(idInputUpload) {
    Swal.fire({
        title: 'Você pode optar por cortar a imagem antes de enviá-la.',
        text: "Deseja cortar?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
    }).then((result) => {
        if (result.isConfirmed) {
            carregar_crop = true;
            $('#' + idInputUpload).click();
            currentInputId = idInputUpload;
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            carregar_crop = false;
            $('#' + idInputUpload).click();
            currentInputId = idInputUpload;
        }

    });
}

function limparImagem(inputId, imgId, removeFieldId) {
    $('#' + inputId).val('');
    $('#' + imgId).attr('src', '/assets/img/person.jpg');
        
    // Marcar para remoção no backend
    if (removeFieldId) {
        $('#' + removeFieldId).val('1');
    }
    
    // Resetar nome original
    originalFileName = null;

    console.log(inputId, imgId, removeFieldId);
}

var idImgViewImg;

$('#uploadImage').change(function () {
    var input = this;
    
    // Resetar flag de remoção ao selecionar novo arquivo
    $('#removeImage').val('0');
    
    // Captura o nome original do arquivo (sem extensão)
    if (input.files && input.files[0]) {
        var originalFile = input.files[0].name;
        originalFileName = originalFile.substring(0, originalFile.lastIndexOf('.')) || originalFile;
    }
    
    if (carregar_crop == true) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                idImgViewImg = "imgPerson"
                $('#cropperImage').attr('src', e.target.result);
                $('#cropModal').modal('show');
            }
            reader.readAsDataURL(input.files[0]);
        }
    } else {
        idImgViewImg = "imgPerson"
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#' + idImgViewImg).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
});

// Logica do crop
var modal_do_crop_concluido = false;

function convertBase64ToJPEGAndSetInputValue(base64data) {
    var base64 = base64data.replace(/^data:image\/(png|jpeg);base64,/, '');

    var blob = base64ToBlob(base64, 'image/jpeg');

    // Usa o nome original do arquivo ao invés de currentInputId
    var filename = (originalFileName || 'image') + '.jpg';

    var file = blobToFile(blob, filename);

    var fileList = new DataTransfer();
    fileList.items.add(file);

    var input = document.getElementById(currentInputId);
    input.files = fileList.files;
}

function base64ToBlob(base64, mimeType) {
    var byteCharacters = atob(base64);
    var byteNumbers = new Array(byteCharacters.length);
    for (var i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    var byteArray = new Uint8Array(byteNumbers);
    return new Blob([byteArray], { type: mimeType });
}

function blobToFile(blob, filename) {
    var file = new File([blob], filename, { type: blob.type });
    return file;
}

$('#cropModal').on('shown.bs.modal', function () {
    var image = document.getElementById('cropperImage');
    modal_do_crop_concluido = false;
    cropper = new Cropper(image, {
        aspectRatio: 1, // Proporção 1:1 para manter a área de recorte quadrada
        viewMode: 1.5, // Define o modo de visualização
        autoCropArea: 1, // A área de recorte ocupará 100% da imagem
        movable: true, // Permite mover a área de recorte
        zoomable: true, // Permite aplicar zoom na imagem
        cropBoxMovable: true, // Permite mover a caixa de recorte
        cropBoxResizable: true, // Permite redimensionar a caixa de recorte
        center: false, // Centraliza a área de recorte
        highlight: true, // Destaca a área de recorte
        background: true // Mostra o fundo da imagem fora da área de recorte
    });
}).on('hidden.bs.modal', function () {
    cropper.destroy();
    cropper = null;
    if (modal_do_crop_concluido == false) {
        var input = document.getElementById(currentInputId);
        input.value = ""
        input.files = null;
    }
});

$('#cropButton').click(function () {
    var canvas = cropper.getCroppedCanvas({
        width: 450,
        height: 450
    });
    canvas.toBlob(function (blob) {
        var url = URL.createObjectURL(blob);
        var reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = function () {
            var base64data = reader.result;
            convertBase64ToJPEGAndSetInputValue(base64data);
            $('#' + idImgViewImg).attr('src', base64data);
            modal_do_crop_concluido = true;
            $('#cropModal').modal('hide');
        }
    });
});

$('.crop_cancel').click(function () {
    $('#cropModal').modal('hide');
    $('#' + currentInputId).val("");
});
