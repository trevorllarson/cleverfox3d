import A11yDialog from "a11y-dialog";

$('.modal').each(function () {
    // fixed position becomes relative to the container when container-type is set, so move our modals out to the body
    $(this).detach().appendTo($("body"));
    let container = $(this)[0];
    let dialog = new A11yDialog(container);
    dialog.on('hide', function (event) {
        const container = event.target;
        let $frame = $(container).find('iframe');
        let src = $frame.attr('src');
        $frame.removeAttr('loading'); // lazy loading will prevent the frame from loading again
        $frame.attr('src', '');
        $frame.attr('src', src);
    });
});

$('.video-modal').each(function () {
    $(this).find('p.visuallyHidden').text($(this).find('iframe').attr('title'));
});
