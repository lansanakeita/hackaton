/**
 * Selection des équipements
 */
$(document).ready(function() {
    $("#office_address_office_equipments").select2({
        placeholder: "Equipements de bureau",
        tags: true,
        tokenSeparators: [",", " "]
    });
});

/**
 * Selection des accès cuisine
 */
$(document).ready(function() {
    $("#office_address_office_kitchens").select2({
        placeholder: "Equipements de cuisine",
        tags: true,
        tokenSeparators: [",", " "]
    });
});