(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
      global.handleApiError = factory()
}(this, function () {

  /**
   * Handles error by showing an alert box.
   * @param {*} error
   * @param {dhtmlXWindowsCell} parentWin
   */
  function errorHandler(error, parentWin = null) {
    console.error(error);
    dhtmlx.alert({
      title: 'Error',
      type: 'alert-error',
      text: typeof error !== 'object' || !error.hasOwnProperty('response') ? error
        : typeof error.response !== 'object' ? error : error.response,
      callback: () => {
        if (parentWin) {
          parentWin.setModal(true)
        }
      }
    })
  }

  return errorHandler
}));
