$.extend(inContextMgr.modals["logout"].modalOptions, { showClose: false });
$.extend(inContextMgr.modals["logout"].events, { onOpen: function () { window.location.reload(); } });