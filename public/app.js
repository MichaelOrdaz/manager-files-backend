require('./bootstrap');

const { codegen } = require('swagger-axios-codegen')
codegen({
  methodNameMode: 'operationId',
  source: require('../../docs/_build/openapi.yaml')
})
