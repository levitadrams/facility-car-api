/**
 * @internal
 * @param {string} functionName
 * @returns {boolean}
 */
function function_exists(functionName)
{
    if(typeof functionName !== 'string') throw new TypeError('functionName must be a string');

    functionName = functionName.trim();

    if(functionName === '') throw new Error('functionName must not be empty');

    if (!/^[a-zA-Z_\x7f-\xff\$][a-zA-Z0-9_\x7f-\xff\$]*$/.test(functionName)) throw new Error('Invalid function name');

    return eval(`typeof ${functionName}`) === 'function';
}

if(! function_exists('onlyNumbers') ) {
    /**
     * @param {*} string
     * @returns {string}
     */
    function onlyNumbers(string)
    {
        if(typeof string.toString !== 'function') return '';

        return string.toString().replace(/\D/g, '');
    }
}

if(! function_exists('createOneTimeForm') ) {
    /**
     * @param {string} action
     * @param {string} method
     * @param {{[name: string]: string}} [inputs]
     * @returns {HTMLFormElement}
     */
    function createOneTimeForm(action, method, inputs = {})
    {
        method = method.toUpperCase();

        const form = document.createElement('form');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.setAttribute('action', action);
        form.setAttribute('method', method === 'GET' ? 'GET' : 'POST');

        form.insertAdjacentHTML('beforeend', `<input type="hidden" name="_token" value="${csrfToken}"/>`);
        form.insertAdjacentHTML('beforeend', `<input type="hidden" name="_method" value="${method}"/>`);

        for (let name in inputs) {
            form.insertAdjacentHTML('beforeend', `<input type="hidden" name="${name}" value="${inputs[name]}"/>`);
        }

        document.body.appendChild(form);

        return form;
    }
}

if(! function_exists('handleDeleteItem') ) {
    /**
     * @param {HTMLElement} el
     * @param {string} route
     * @param {string} [message]
     * @returns {void}
     */
    function handleDeleteItem(el, route, message = 'Deseja realmente deletar este item?')
    {
        const id = el.getAttribute('data-id');

        if (!confirm(message)) return false;

        createOneTimeForm(route, 'DELETE').submit();
    };
}

if(! function_exists('handleToggleItem') ) {
    /**
     * @param {HTMLElement} el
     * @param {string} route
     * @param {string} [message]
     * @returns {void}
     */
    function handleToggleItem(el, route, message = 'Deseja realmente ativar/desativar este item?')
    {
        const id = el.getAttribute('data-id');

        if (!confirm(message)) return false;

        createOneTimeForm(route, 'PUT').submit();
    };
}

if(! function_exists('handleMoveUpItem') ) {
    /**
     * @param {HTMLElement} el
     * @param {string} route
     * @returns {void}
     */
    function handleMoveUpItem(el, route)
    {
        const id = el.getAttribute('data-id');

        createOneTimeForm(route, 'PUT').submit();
    };
}

if(! function_exists('handleMoveDownItem') ) {
    /**
     * @param {HTMLElement} el
     * @param {string} route
     * @returns {void}
     */
    function handleMoveDownItem(el, route)
    {
        const id = el.getAttribute('data-id');

        createOneTimeForm(route, 'PUT').submit();
    };
}

if(! function_exists('copyToClipboard') ) {
    /**
     * @callback successCallback
     * @returns {void}
     */

    /**
     * @param {string} text
     * @param {successCallback?} successCallback
     * @returns {true}
     */
    window.copyToClipboard = async function(text, successCallback = null)
    {
        if (navigator.clipboard) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (err) {
                syncCopyToClipboard(text);
            }
        } else {
            syncCopyToClipboard(text);
        }

        if(successCallback) {
            successCallback();
        }

        return true;
    }
}

if(! function_exists('syncCopyToClipboard') ) {
    /**
     * @param {string} text
     * @returns {void}
     * @deprecated
     */
    function syncCopyToClipboard(text)
    {
        const container = document.querySelector('.modal.show') || document.body;

        let temp_textarea = document.createElement('textarea');

        container.appendChild(temp_textarea);
        temp_textarea.value = text;
        temp_textarea.select();
        document.execCommand('copy');
        container.removeChild(temp_textarea);
    }
}

if(! function_exists('qrCodeAPI') ) {
    /**
     * @param {string} data
     * @param {number?} width
     * @param {number?} height
     * @throws Will throw an error if the first argument is undefined.
     * @returns {string}
     */
    function qrCodeAPI(data, width = null, height = null)
    {
        if(!data) throw new Error('data must not be empty');

        width = Math.floor(width);
        width = isNaN(width) ? 200 : width;

        if(height) {
            height = Math.floor(height);
            height = isNaN(height) ? width : height;
        } else {
            height = width;
        }

        return `https://api.qrserver.com/v1/create-qr-code/?size=${width}x${height}&data=${encodeURI(data)}`;
    }
}

if(! function_exists('formatMoney') ) {
    /**
     * @param {number|bigint} value
     * @returns
     */
    function formatMoney(value)
    {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }
}

if(! function_exists('viaCepApi') ) {
    /**
     * @typedef {Object} ViaCepAPIOKResponse
     * @property {string} data.cep
     * @property {string} data.logradouro
     * @property {string} data.complemento
     * @property {string} data.bairro
     * @property {string} data.localidade
     * @property {string} data.uf
     * @property {string} data.ibge
     * @property {string} data.gia
     * @property {string} data.ddd
     * @property {string} data.siafi
     */

    /**
     * @callback successCallback
     * @param {ViaCepAPIOKResponse} data
     * @returns {void}
     */

    /**
     * @callback errorCallback
     * @returns {void}
     */

    /**
     * @param {string} cep
     * @param {successCallback?} successCallback
     * @param {errorCallback?} errorCallback
     * @returns {boolean}
     */
    window.viaCepApi = async function(cep, successCallback = null, errorCallback = null)
    {
        cep = onlyNumbers(cep);

        // Valida formato básico do CEP (8 dígitos)
        if (!cep || cep.length !== 8) {
            if(errorCallback) {
                errorCallback();
            } else {
                iziToast.error({
                    title: 'Erro!',
                    message: 'CEP inválido - digite 8 dígitos',
                    position: 'topRight'
                });
            }
            return false;
        }

        try {
            // Usa a API interna com dual-provider fallback (BrasilAPI → ViaCEP)
            const response = await fetch(`/api/cep/${cep}`);
            const result = await response.json();

            // Se CEP foi encontrado
            if (result.valid && result.data) {
                if(successCallback) {
                    successCallback(result.data);
                }
                return true;
            }

            // Se CEP não foi encontrado mas permite fallback (erro técnico)
            if (result.fallback) {
                if(errorCallback) {
                    errorCallback();
                } else {
                    iziToast.warning({
                        title: 'Atenção!',
                        message: 'CEP não pôde ser consultado. Preencha manualmente.',
                        position: 'topRight',
                        timeout: 5000
                    });
                }
                return false;
            }

            // CEP inválido (não existe em nenhuma API)
            if(errorCallback) {
                errorCallback();
            } else {
                iziToast.error({
                    title: 'Erro!',
                    message: 'CEP não encontrado',
                    position: 'topRight'
                });
            }

            return false;
        } catch(e) {
            // Erro de rede ou exceção - permite entrada manual
            if(errorCallback) {
                errorCallback();
            } else {
                iziToast.error({
                    title: 'Erro!',
                    message: 'Erro ao consultar CEP. Preencha manualmente.',
                    position: 'topRight'
                });
            }

            return false;
        }
    }
}

if(! function_exists('removeTableRow') ) {
    /**
     * @callback onFadeOut
     * @returns {void}
     */

    /**
     * @param {HTMLElement} btn
     * @param {number} animationTime Set to 0 to remove instantly
     * @param {onFadeOut?} onFadeOut
     */
    function removeTableRow(btn, animationTime = 500, onFadeOut = null)
    {
        const $tr = $(btn).closest('tr');

        if(animationTime <= 0) {
            $tr.remove();
        } else {
            onFadeOut ||= function() {
                this.remove();
            }

            $tr.fadeOut(animationTime, onFadeOut);
        }
    }
}

if(! function_exists('constrain') ) {
    /**
     * @param {number} value
     * @param {number} min
     * @param {number} max
     * @returns {number}
     */
    function constrain(value, min, max)
    {
        return Math.max(Math.min(value, max), min);
    }
}

if(! function_exists('getYoutubeIdFromUrl') ) {
    /**
     * @param {string} url
     * @returns {string?}
     */
    function getYoutubeIdFromUrl(url)
    {
        let id = null;

        try {
            const _url = new URL(url)

            if(_url.origin === 'https://www.youtube.com') {
                if(_url.pathname === '/watch') {
                    id = _url.searchParams.get('v');
                } else if(_url.pathname.startsWith('/live')) {
                    id = _url.pathname.split('/')[2];
                }
            } else if(_url.origin === 'https://youtu.be') {
                id = _url.pathname.substring(1);
            }
        } catch(e) {
            console.error(e);
        }

        return id;
    }
}

if(! function_exists('capitalize') ) {
    /**
     * @param {string} string
     * @returns {string}
     */
    function capitalize(string) {
        return string.replace(/(?<=[-_\s])[a-zá-ù]|^[a-zá-ù]/g, char => char.toUpperCase());
    }
}

if(! function_exists('formatDate') ) {
    /**
     * @param {string} date Format: YYYY-MM-DD
     * @returns {Date}
     *
     * @throws {RangeError}
     */
    function formatDate(date)
    {
        const regex = /\d{4}-\d{2}-\d{2}$/;
        if(!regex.test(date)) throw new RangeError('Invalid date format');

        return new Date(`${date}T00:00:00-03:00`);
    }
}

if(! function_exists('map') ) {
    /**
     * @param {number} value Value being mapped
     * @param {number} min_value Minimum input value
     * @param {number} max_value Maximum input value
     * @param {number} min_constrained Minimum output value
     * @param {number} max_constrained Maximum output value
     */
    function map(value, min_value, max_value, min_constrained, max_constrained)
    {
        return ((value - min_value) * (max_constrained - min_constrained) / (max_value - min_value)) + min_constrained;
    }
}

if(! function_exists('slugify') ) {
    /**
     * @param {string} value
     * @returns {string}
     */
    function slugify(value)
    {
        return value.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
    }
}
