window.Csrf = {
    get name()  { return document.querySelector('meta[name="csrf-name"]').content; },
    get token() { return document.querySelector('meta[name="csrf-token"]').content; }
};