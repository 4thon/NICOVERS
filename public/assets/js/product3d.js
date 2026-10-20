import * as THREE from 'three';
import { OrbitControls } from 'https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/controls/OrbitControls.js';

document.querySelectorAll('.product-viewer, .modal-product-viewer').forEach((container) => {
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(42, 1, 0.1, 100);
  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  container.appendChild(renderer.domElement);

  const controls = new OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;
  controls.autoRotate = true;
  controls.autoRotateSpeed = 1.1;
  controls.minDistance = 2.4;
  controls.maxDistance = 7;
  controls.target.set(0, 0, 0);

  scene.add(new THREE.HemisphereLight(0xfffcf5, 0x51483f, 2.1));
  const sun = new THREE.DirectionalLight(0xffffff, 2.7);
  sun.position.set(4, 7, 5);
  scene.add(sun);
  const fill = new THREE.DirectionalLight(0xffb258, 1.1);
  fill.position.set(-5, 2, 2);
  scene.add(fill);

  const product = new THREE.Group();
  scene.add(product);
  const dark = new THREE.MeshStandardMaterial({ color: 0x172638, roughness: 0.42, metalness: 0.25 });
  const metal = new THREE.MeshStandardMaterial({ color: 0xb7bec2, roughness: 0.3, metalness: 0.78 });
  const orange = new THREE.MeshStandardMaterial({ color: 0xf2760f, roughness: 0.45, metalness: 0.18 });
  const battery = new THREE.MeshStandardMaterial({ color: 0xf2eee7, roughness: 0.5, metalness: 0.3 });

  function addPanel() {
    const frame = new THREE.Mesh(new THREE.BoxGeometry(3.2, 0.12, 1.9), metal);
    product.add(frame);
    const panel = new THREE.Mesh(new THREE.BoxGeometry(3.04, 0.08, 1.74), dark);
    panel.position.y = 0.09;
    product.add(panel);
    const cellMaterial = new THREE.MeshStandardMaterial({ color: 0x29445d, roughness: 0.32, metalness: 0.18 });
    for (let x = -1.28; x <= 1.28; x += 0.43) for (let z = -0.65; z <= 0.65; z += 0.33) {
      const cell = new THREE.Mesh(new THREE.BoxGeometry(0.36, 0.012, 0.27), cellMaterial);
      cell.position.set(x, 0.145, z);
      product.add(cell);
    }
    product.rotation.x = -0.32;
  }

  function addInverter() {
    const body = new THREE.Mesh(new THREE.BoxGeometry(1.8, 2.25, 0.55), battery);
    product.add(body);
    const screen = new THREE.Mesh(new THREE.BoxGeometry(0.72, 0.42, 0.04), dark);
    screen.position.set(0, 0.45, 0.3);
    product.add(screen);
    [-0.48, 0, 0.48].forEach((x) => { const port = new THREE.Mesh(new THREE.CylinderGeometry(0.1, 0.1, 0.12, 20), orange); port.rotation.x = Math.PI / 2; port.position.set(x, -0.75, 0.34); product.add(port); });
  }

  function addBattery() {
    const body = new THREE.Mesh(new THREE.BoxGeometry(2, 2.5, 1.05), battery);
    product.add(body);
    const seam = new THREE.Mesh(new THREE.BoxGeometry(2.04, 0.06, 1.09), metal);
    seam.position.y = 0.15;
    product.add(seam);
    const handle = new THREE.Mesh(new THREE.TorusGeometry(0.28, 0.045, 12, 20, Math.PI), dark);
    handle.position.set(0, 1.05, 0.57);
    product.add(handle);
  }

  function addMounting() {
    const rail = new THREE.Mesh(new THREE.BoxGeometry(3.5, 0.16, 0.22), metal);
    product.add(rail);
    [-1.15, 0, 1.15].forEach((x) => { const bracket = new THREE.Mesh(new THREE.BoxGeometry(0.16, 1.3, 0.5), metal); bracket.position.set(x, -0.63, 0); product.add(bracket); });
    const clamp = new THREE.Mesh(new THREE.BoxGeometry(0.3, 0.42, 0.55), orange);
    clamp.position.y = 0.27;
    product.add(clamp);
    product.rotation.y = -0.24;
  }

  function addWire() {
    const curve = new THREE.CatmullRomCurve3([new THREE.Vector3(-1.3, 0, 0), new THREE.Vector3(-.6, .6, .3), new THREE.Vector3(.2, -.4, -.1), new THREE.Vector3(1.3, .15, 0)]);
    const cable = new THREE.Mesh(new THREE.TubeGeometry(curve, 64, .11, 12, false), dark);
    product.add(cable);
    [-1.38, 1.37].forEach((x) => { const connector = new THREE.Mesh(new THREE.CylinderGeometry(.16, .16, .42, 18), orange); connector.rotation.z = Math.PI / 2; connector.position.set(x, x < 0 ? -.03 : .15, 0); product.add(connector); });
  }

  const type = container.dataset.productType;
  if (type === 'inverters') addInverter();
  else if (type === 'batteries') addBattery();
  else if (type === 'mounting') addMounting();
  else if (type === 'wires') addWire();
  else addPanel();

  function resetView() { camera.position.set(3.7, 2.8, 4.4); controls.target.set(0, 0, 0); controls.update(); }
  resetView();
  if (container.id === 'productViewer') document.querySelector('[data-reset-view]')?.addEventListener('click', resetView);

  function resize() { const width = container.clientWidth; const height = container.clientHeight; camera.aspect = width / height; camera.updateProjectionMatrix(); renderer.setSize(width, height, false); }
  new ResizeObserver(resize).observe(container);
  resize();
  (function animate() { requestAnimationFrame(animate); controls.update(); renderer.render(scene, camera); })();
});
