import SecureLS from "secure-ls";

let _ls: SecureLS | null = null;
function ls() {
  if (process.client) {
    if (!_ls) _ls = new SecureLS({ encodingType: "aes", isCompression: true });
    return _ls;
  }
  return null;
}

export const secureLsStorage = {
  getItem: (key: string) => {
    const s = ls();
    if (!s) return null;
    try { return s.get(key); } catch { return null; }
  },
  setItem: (key: string, value: any) => {
    const s = ls();
    if (!s) return;
    try { s.set(key, value); } catch {}
  },
  removeItem: (key: string) => {
    const s = ls();
    if (!s) return;
    try { s.remove(key); } catch {}
  },
};
