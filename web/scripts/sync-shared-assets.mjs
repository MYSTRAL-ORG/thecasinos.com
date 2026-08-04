import { cp, mkdir, stat } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const scriptDirectory = dirname(fileURLToPath(import.meta.url));
const source = resolve(scriptDirectory, '../../public/cards');
const destination = resolve(scriptDirectory, '../public/cards');

try {
  await stat(source);
} catch {
  throw new Error(`Shared card assets were not found at ${source}`);
}

await mkdir(destination, { recursive: true });
await cp(source, destination, { recursive: true, force: true });

console.log('Shared card assets synced to web/public/cards.');
