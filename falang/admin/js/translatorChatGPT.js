/*Translate GGPT use the async/await/fetch system */
async function translateService(fieldName, sourceText) {

    const data = {
        action: 'service_translate', // use to find the filter
        service: 'chatgpt',
        sourceLanguageCode: translator.from,
        targetLanguageLocale: translator.to,
        text: sourceText
    };

    try {
        document.body.classList.add('waiting');

        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        });

        const result = await response.json();

        if (result.success) {
            console.log(result);
            setTranslation(fieldName, result.data);
        } else {
            console.log('Error : ' + result.data);
            setTranslation(fieldName, result.data);
        }

    } catch (error) {
        const translatedText = "ERROR : " + error.message;
        setTranslation(fieldName, translatedText);
        console.log(error);

    } finally {
        document.body.classList.remove('waiting');
    }
}