$(document).ready(function() {

    $('.select2').select2();

    //----------------- Digitar somente Número --------------------------
    $('.numeric').keyup(function () {
        $(this).val(this.value.replace(/\D/g, ''));
    });

//----------------- Digitar somente Letras --------------------------
    $('.letter').keyup(function () {
        this.value = this.value.replace(/[^A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]/g, '');
    });

    $(".mask_value").maskMoney({allowNegative: true, thousands:'.', decimal:',', affixesStay: false});

});