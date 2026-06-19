$(document).ready(function () {
    // ----------- Mascara de campos ------------------
    $('#church_phone').mask('(99) 9999-9999?9');
    $('#mobile').mask('(99) 9999-9999?9');
    $('#mobile_responsible').mask('(99) 9999-9999?9');
    $('#zipcode').mask('99999-999');

    //----------------- busca CEP --------------------------
    $('#zipcode').on('blur', function() {
        if ($(this).val().length === 9){
            viaCepApi($(this).val().replace('-', ''))
        }
    });

    //----------------- email global -----------------
    let email = '';
    $(window).on('load', function () {
        email = $('#email').val();
    });

    //----------------- search in the database the email-----------------
    $("#email").on("blur", function () {
        const email2 = $(this).val();
        if (email !== email2) {
            $.ajax({
                type: 'GET',
                url: `/admin/igreja/buscarEmail`,
                data: { email: email2 },
                dataType: 'json',
                success: function (data) {
                    const emailFound = data[0]?.email;
                    if (emailFound) {
                        iziToast.error({
                            position: 'center',
                            title: `Email existente: ${emailFound}`,
                            timeout: false,
                        });
                        $('#email').val('').focus();
                    }
                }
            });
        }
    });

    //-------------------------- consutar cnpj ---------------------------------
    // $("#cpf_cnpj").focusout(function (e) {
    //     e.preventDefault();
    //     const cnpj = $(this).val().replace(/[^0-9]/g, '');
    //     if (cnpj.length === 14) {
    //         $.ajax({
    //             url: `https://www.receitaws.com.br/v1/cnpj/${cnpj}`,
    //             method: 'GET',
    //             dataType: 'jsonp',
    //             complete: function (xhr) {
    //                 const response = xhr.responseJSON;
    //                 if (response.status === 'OK') {
    //                     $('#name').val(response.nome);
    //                     $('#zipcode').val(response.cep);
    //                     $('#street').val(response.logradouro);
    //                     $('#number').val(response.numero);
    //                     $('#complement').val(response.complemento);
    //                     $('#city').val(response.municipio);
    //                     $('#state').val(response.uf);
    //                     $('#neighborhood').val(response.bairro);
    //                     $('#nickname').focus();
    //                 } else {
    //                     iziToast.error({
    //                         title: 'Atenção!',
    //                         message: 'CNPJ não encontrado!',
    //                         position: 'center',
    //                         timeout: 4000,
    //                     });
    //                     $("#cpf_cnpj").val('').focus();
    //                 }
    //             }
    //         });
    //     }
    // });

    //----------------- Mascara CPF CNPJ --------------------------
    $("#cpf_cnpj, #cpf_cnpj_responsible").on('keypress', function () {
        mascaraMutuario(this, cpfCnpj);
    });
    $(window).on('load', function () {
        $('#cpf_cnpj').trigger('keypress');
        $('#cpf_cnpj_responsible').trigger('keypress');
    });

    function mascaraMutuario(o, f) {
        setTimeout(() => {
            o.value = f(o.value);
        }, 1);
    }

    function cpfCnpj(v) {
        v = v.replace(/\D/g, "");
        if (v.length <= 11) {
            v = v.replace(/(\d{3})(\d)/, "$1.$2")
                 .replace(/(\d{3})(\d)/, "$1.$2")
                 .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        } else {
            v = v.replace(/^(\d{2})(\d)/, "$1.$2")
                 .replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3")
                 .replace(/\.(\d{3})(\d)/, ".$1/$2")
                 .replace(/(\d{4})(\d)/, "$1-$2");
        }
        return v;
    }

    //----------------- function buscar CEP --------------------------
    const viaCepApi = (cep) => {
        $.ajax({
            url: '/api/cep/' + cep, 
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                // CEP encontrado com sucesso
                if (result.valid && result.data) {
                    $('#street').val(result.data.logradouro);
                    $('#neighborhood').val(result.data.bairro);
                    $('#city').val(result.data.localidade);
                    $('#state').val(result.data.uf);
                    $('#number').focus();
                    return;
                }

                // CEP não encontrado - verifica se permite fallback
                if (result.fallback) {
                    // Erro técnico - permite entrada manual
                    iziToast.warning({
                        title: 'Atenção!',
                        message: 'CEP não pôde ser consultado. Preencha manualmente.',
                        position: 'topRight',
                        timeout: 5000
                    });
                    // Desbloqueia campos para entrada manual
                    $('#street, #neighborhood, #city, #state').prop('readonly', false);
                    $('#street').focus();
                    return;
                }

                // CEP inválido (não existe)
                iziToast.error({
                    title: 'Erro!',
                    message: 'CEP não encontrado',
                    position: 'topRight'
                });
            },
            error: function(xhr) {
                // Erro de rede - permite entrada manual
                iziToast.error({
                    title: 'Erro!',
                    message: 'Erro ao consultar CEP. Preencha manualmente.',
                    position: 'topRight'
                });
                // Desbloqueia campos para entrada manual
                $('#street, #neighborhood, #city, #state').prop('readonly', false);
                $('#street').focus();
            }
        });
    }
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
    $('#' + imgId).attr('src', '/assets/img/img_default.png');
    
    // Marcar para remoção no backend
    if (removeFieldId) {
        $('#' + removeFieldId).val('1');
    }
    
    // Resetar nome original
    originalFileName = null;
}

var idImgViewImg;

$('#uploadImage').change(function () {
    var input = this;
    
    // Resetar flag de remoção ao selecionar novo arquivo
    $('#removeLogo').val('0');
    
    // Captura o nome original do arquivo (sem extensão)
    if (input.files && input.files[0]) {
        var originalFile = input.files[0].name;
        originalFileName = originalFile.substring(0, originalFile.lastIndexOf('.')) || originalFile;
    }
    
    if (carregar_crop == true) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                idImgViewImg = "imgLogo"
                $('#cropperImage').attr('src', e.target.result);
                $('#cropModal').modal('show');
            }
            reader.readAsDataURL(input.files[0]);
        }
    } else {
        idImgViewImg = "imgLogo"
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