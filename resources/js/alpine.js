const Alpine = window.Alpine

window.data = (name, definition) => Alpine.data(name, definition)
window.store = (name, value) => Alpine.store(name, value)
window.directive = (name, callback) => Alpine.directive(name, callback)
window.magic = (name, callback) => Alpine.magic(name, callback)

export default Alpine
