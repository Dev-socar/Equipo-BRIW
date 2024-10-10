$(document).ready(function() {
    $('#uploadForm').on('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        console.log(formData)
        console.log($(this).attr('action'))

        $.ajax({
            url: $(this).attr('action'), 
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Archivo enviado correctamente:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error al enviar el archivo:', error);
            }
        });
    });
});

$("#file").on("change", () => {
    $name = $('#file').val().split('\\').pop();
    $("#fileName").text($name);
})