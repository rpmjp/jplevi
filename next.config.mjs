/** @type {import('next').NextConfig} */
const nextConfig = {
  // Static export: emits a fully static `out/` folder for Apache shared hosting.
  output: "export",
  trailingSlash: true,
  images: {
    unoptimized: true,
  },
  reactStrictMode: true,
};

export default nextConfig;
