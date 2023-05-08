module.exports = (api) => {
  // This caches the Babel config
  api.cache.using(() => process.env.NODE_ENV);

  return {
    presets: [
      "@babel/preset-env",
      // Enable development transform of React with new automatic runtime
      [
        "@babel/preset-react",
        { development: !api.env("production"), runtime: "automatic", "throwIfNamespace": false },
      ],
      "@babel/preset-typescript",
    ],
    plugins: [
      "@babel/plugin-proposal-class-properties",
      "@babel/plugin-proposal-export-default-from",
      "@babel/plugin-syntax-dynamic-import",
    ],
  };
};
