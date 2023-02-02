<?php
// This file was auto-generated from sdk-root/src/data/gamelift/2015-10-01/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2015-10-01', 'endpointPrefix' => 'gamelift', 'jsonVersion' => '1.1', 'serviceFullName' => 'Amazon GameLift', 'signatureVersion' => 'v4', 'targetPrefix' => 'GameLift', 'protocol' => 'json', ], 'operations' => [ 'CreateAlias' => [ 'name' => 'CreateAlias', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateAliasInput', ], 'output' => [ 'shape' => 'CreateAliasOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'LimitExceededException', 'exception' => true, ], ], ], 'CreateBuild' => [ 'name' => 'CreateBuild', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateBuildInput', ], 'output' => [ 'shape' => 'CreateBuildOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'CreateFleet' => [ 'name' => 'CreateFleet', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateFleetInput', ], 'output' => [ 'shape' => 'CreateFleetOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'LimitExceededException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'CreateGameSession' => [ 'name' => 'CreateGameSession', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateGameSessionInput', ], 'output' => [ 'shape' => 'CreateGameSessionOutput', ], 'errors' => [ [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidFleetStatusException', 'exception' => true, ], [ 'shape' => 'TerminalRoutingStrategyException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'FleetCapacityExceededException', 'exception' => true, ], ], ], 'CreatePlayerSession' => [ 'name' => 'CreatePlayerSession', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreatePlayerSessionInput', ], 'output' => [ 'shape' => 'CreatePlayerSessionOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidGameSessionStatusException', 'exception' => true, ], [ 'shape' => 'GameSessionFullException', 'exception' => true, ], [ 'shape' => 'TerminalRoutingStrategyException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], ], ], 'CreatePlayerSessions' => [ 'name' => 'CreatePlayerSessions', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreatePlayerSessionsInput', ], 'output' => [ 'shape' => 'CreatePlayerSessionsOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidGameSessionStatusException', 'exception' => true, ], [ 'shape' => 'GameSessionFullException', 'exception' => true, ], [ 'shape' => 'TerminalRoutingStrategyException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], ], ], 'DeleteAlias' => [ 'name' => 'DeleteAlias', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteAliasInput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'DeleteBuild' => [ 'name' => 'DeleteBuild', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteBuildInput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], ], ], 'DeleteFleet' => [ 'name' => 'DeleteFleet', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteFleetInput', ], 'errors' => [ [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'InvalidFleetStatusException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'InvalidFleetStatusException', 'exception' => true, ], ], ], 'DescribeAlias' => [ 'name' => 'DescribeAlias', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeAliasInput', ], 'output' => [ 'shape' => 'DescribeAliasOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'DescribeBuild' => [ 'name' => 'DescribeBuild', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeBuildInput', ], 'output' => [ 'shape' => 'DescribeBuildOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'DescribeEC2InstanceLimits' => [ 'name' => 'DescribeEC2InstanceLimits', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeEC2InstanceLimitsInput', ], 'output' => [ 'shape' => 'DescribeEC2InstanceLimitsOutput', ], 'errors' => [ [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'DescribeFleetAttributes' => [ 'name' => 'DescribeFleetAttributes', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeFleetAttributesInput', ], 'output' => [ 'shape' => 'DescribeFleetAttributesOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'DescribeFleetCapacity' => [ 'name' => 'DescribeFleetCapacity', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeFleetCapacityInput', ], 'output' => [ 'shape' => 'DescribeFleetCapacityOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'DescribeFleetEvents' => [ 'name' => 'DescribeFleetEvents', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeFleetEventsInput', ], 'output' => [ 'shape' => 'DescribeFleetEventsOutput', ], 'errors' => [ [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], ], ], 'DescribeFleetPortSettings' => [ 'name' => 'DescribeFleetPortSettings', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeFleetPortSettingsInput', ], 'output' => [ 'shape' => 'DescribeFleetPortSettingsOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'DescribeFleetUtilization' => [ 'name' => 'DescribeFleetUtilization', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeFleetUtilizationInput', ], 'output' => [ 'shape' => 'DescribeFleetUtilizationOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'DescribeGameSessions' => [ 'name' => 'DescribeGameSessions', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeGameSessionsInput', ], 'output' => [ 'shape' => 'DescribeGameSessionsOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'TerminalRoutingStrategyException', 'exception' => true, ], ], ], 'DescribePlayerSessions' => [ 'name' => 'DescribePlayerSessions', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribePlayerSessionsInput', ], 'output' => [ 'shape' => 'DescribePlayerSessionsOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'GetGameSessionLogUrl' => [ 'name' => 'GetGameSessionLogUrl', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'GetGameSessionLogUrlInput', ], 'output' => [ 'shape' => 'GetGameSessionLogUrlOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], ], ], 'ListAliases' => [ 'name' => 'ListAliases', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListAliasesInput', ], 'output' => [ 'shape' => 'ListAliasesOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'ListBuilds' => [ 'name' => 'ListBuilds', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListBuildsInput', ], 'output' => [ 'shape' => 'ListBuildsOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'ListFleets' => [ 'name' => 'ListFleets', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListFleetsInput', ], 'output' => [ 'shape' => 'ListFleetsOutput', ], 'errors' => [ [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'RequestUploadCredentials' => [ 'name' => 'RequestUploadCredentials', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'RequestUploadCredentialsInput', ], 'output' => [ 'shape' => 'RequestUploadCredentialsOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'ResolveAlias' => [ 'name' => 'ResolveAlias', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ResolveAliasInput', ], 'output' => [ 'shape' => 'ResolveAliasOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'TerminalRoutingStrategyException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'UpdateAlias' => [ 'name' => 'UpdateAlias', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateAliasInput', ], 'output' => [ 'shape' => 'UpdateAliasOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'UpdateBuild' => [ 'name' => 'UpdateBuild', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateBuildInput', ], 'output' => [ 'shape' => 'UpdateBuildOutput', ], 'errors' => [ [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], ], ], 'UpdateFleetAttributes' => [ 'name' => 'UpdateFleetAttributes', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateFleetAttributesInput', ], 'output' => [ 'shape' => 'UpdateFleetAttributesOutput', ], 'errors' => [ [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'InvalidFleetStatusException', 'exception' => true, ], [ 'shape' => 'LimitExceededException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'UpdateFleetCapacity' => [ 'name' => 'UpdateFleetCapacity', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateFleetCapacityInput', ], 'output' => [ 'shape' => 'UpdateFleetCapacityOutput', ], 'errors' => [ [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'LimitExceededException', 'exception' => true, ], [ 'shape' => 'InvalidFleetStatusException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'UpdateFleetPortSettings' => [ 'name' => 'UpdateFleetPortSettings', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateFleetPortSettingsInput', ], 'output' => [ 'shape' => 'UpdateFleetPortSettingsOutput', ], 'errors' => [ [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'InvalidFleetStatusException', 'exception' => true, ], [ 'shape' => 'LimitExceededException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], ], ], 'UpdateGameSession' => [ 'name' => 'UpdateGameSession', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateGameSessionInput', ], 'output' => [ 'shape' => 'UpdateGameSessionOutput', ], 'errors' => [ [ 'shape' => 'NotFoundException', 'exception' => true, ], [ 'shape' => 'ConflictException', 'exception' => true, ], [ 'shape' => 'InternalServiceException', 'exception' => true, 'fault' => true, ], [ 'shape' => 'UnauthorizedException', 'exception' => true, ], [ 'shape' => 'InvalidGameSessionStatusException', 'exception' => true, ], [ 'shape' => 'InvalidRequestException', 'exception' => true, ], ], ], ], 'shapes' => [ 'Alias' => [ 'type' => 'structure', 'members' => [ 'AliasId' => [ 'shape' => 'AliasId', ], 'Name' => [ 'shape' => 'FreeText', ], 'Description' => [ 'shape' => 'FreeText', ], 'RoutingStrategy' => [ 'shape' => 'RoutingStrategy', ], 'CreationTime' => [ 'shape' => 'Timestamp', ], 'LastUpdatedTime' => [ 'shape' => 'Timestamp', ], ], ], 'AliasId' => [ 'type' => 'string', 'pattern' => '^alias-\\S+', ], 'AliasList' => [ 'type' => 'list', 'member' => [ 'shape' => 'Alias', ], ], 'AwsCredentials' => [ 'type' => 'structure', 'members' => [ 'AccessKeyId' => [ 'shape' => 'NonEmptyString', ], 'SecretAccessKey' => [ 'shape' => 'NonEmptyString', ], 'SessionToken' => [ 'shape' => 'NonEmptyString', ], ], 'sensitive' => true, ], 'Build' => [ 'type' => 'structure', 'members' => [ 'BuildId' => [ 'shape' => 'BuildId', ], 'Name' => [ 'shape' => 'FreeText', ], 'Version' => [ 'shape' => 'FreeText', ], 'Status' => [ 'shape' => 'BuildStatus', ], 'SizeOnDisk' => [ 'shape' => 'PositiveLong', ], 'CreationTime' => [ 'shape' => 'Timestamp', ], ], ], 'BuildId' => [ 'type' => 'string', 'pattern' => '^build-\\S+', ], 'BuildList' => [ 'type' => 'list', 'member' => [ 'shape' => 'Build', ], ], 'BuildStatus' => [ 'type' => 'string', 'enum' => [ 'INITIALIZED', 'READY', 'FAILED', ], ], 'ConflictException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'CreateAliasInput' => [ 'type' => 'structure', 'required' => [ 'Name', ], 'members' => [ 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'Description' => [ 'shape' => 'NonZeroAndMaxString', ], 'RoutingStrategy' => [ 'shape' => 'RoutingStrategy', ], ], ], 'CreateAliasOutput' => [ 'type' => 'structure', 'members' => [ 'Alias' => [ 'shape' => 'Alias', ], ], ], 'CreateBuildInput' => [ 'type' => 'structure', 'members' => [ 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'Version' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'CreateBuildOutput' => [ 'type' => 'structure', 'members' => [ 'Build' => [ 'shape' => 'Build', ], 'UploadCredentials' => [ 'shape' => 'AwsCredentials', ], 'StorageLocation' => [ 'shape' => 'S3Location', ], ], ], 'CreateFleetInput' => [ 'type' => 'structure', 'required' => [ 'Name', 'BuildId', 'ServerLaunchPath', 'EC2InstanceType', ], 'members' => [ 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'Description' => [ 'shape' => 'NonZeroAndMaxString', ], 'BuildId' => [ 'shape' => 'BuildId', ], 'ServerLaunchPath' => [ 'shape' => 'NonZeroAndMaxString', ], 'ServerLaunchParameters' => [ 'shape' => 'NonZeroAndMaxString', ], 'LogPaths' => [ 'shape' => 'StringList', ], 'EC2InstanceType' => [ 'shape' => 'EC2InstanceType', ], 'EC2InboundPermissions' => [ 'shape' => 'IpPermissionsList', ], ], ], 'CreateFleetOutput' => [ 'type' => 'structure', 'members' => [ 'FleetAttributes' => [ 'shape' => 'FleetAttributes', ], ], ], 'CreateGameSessionInput' => [ 'type' => 'structure', 'required' => [ 'MaximumPlayerSessionCount', ], 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'AliasId' => [ 'shape' => 'AliasId', ], 'MaximumPlayerSessionCount' => [ 'shape' => 'WholeNumber', ], 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'GameProperties' => [ 'shape' => 'GamePropertyList', ], ], ], 'CreateGameSessionOutput' => [ 'type' => 'structure', 'members' => [ 'GameSession' => [ 'shape' => 'GameSession', ], ], ], 'CreatePlayerSessionInput' => [ 'type' => 'structure', 'required' => [ 'GameSessionId', 'PlayerId', ], 'members' => [ 'GameSessionId' => [ 'shape' => 'GameSessionId', ], 'PlayerId' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'CreatePlayerSessionOutput' => [ 'type' => 'structure', 'members' => [ 'PlayerSession' => [ 'shape' => 'PlayerSession', ], ], ], 'CreatePlayerSessionsInput' => [ 'type' => 'structure', 'required' => [ 'GameSessionId', 'PlayerIds', ], 'members' => [ 'GameSessionId' => [ 'shape' => 'GameSessionId', ], 'PlayerIds' => [ 'shape' => 'PlayerIdList', ], ], ], 'CreatePlayerSessionsOutput' => [ 'type' => 'structure', 'members' => [ 'PlayerSessions' => [ 'shape' => 'PlayerSessionList', ], ], ], 'DeleteAliasInput' => [ 'type' => 'structure', 'required' => [ 'AliasId', ], 'members' => [ 'AliasId' => [ 'shape' => 'AliasId', ], ], ], 'DeleteBuildInput' => [ 'type' => 'structure', 'required' => [ 'BuildId', ], 'members' => [ 'BuildId' => [ 'shape' => 'BuildId', ], ], ], 'DeleteFleetInput' => [ 'type' => 'structure', 'required' => [ 'FleetId', ], 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], ], ], 'DescribeAliasInput' => [ 'type' => 'structure', 'required' => [ 'AliasId', ], 'members' => [ 'AliasId' => [ 'shape' => 'AliasId', ], ], ], 'DescribeAliasOutput' => [ 'type' => 'structure', 'members' => [ 'Alias' => [ 'shape' => 'Alias', ], ], ], 'DescribeBuildInput' => [ 'type' => 'structure', 'required' => [ 'BuildId', ], 'members' => [ 'BuildId' => [ 'shape' => 'BuildId', ], ], ], 'DescribeBuildOutput' => [ 'type' => 'structure', 'members' => [ 'Build' => [ 'shape' => 'Build', ], ], ], 'DescribeEC2InstanceLimitsInput' => [ 'type' => 'structure', 'members' => [ 'EC2InstanceType' => [ 'shape' => 'EC2InstanceType', ], ], ], 'DescribeEC2InstanceLimitsOutput' => [ 'type' => 'structure', 'members' => [ 'EC2InstanceLimits' => [ 'shape' => 'EC2InstanceLimitList', ], ], ], 'DescribeFleetAttributesInput' => [ 'type' => 'structure', 'members' => [ 'FleetIds' => [ 'shape' => 'FleetIdList', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeFleetAttributesOutput' => [ 'type' => 'structure', 'members' => [ 'FleetAttributes' => [ 'shape' => 'FleetAttributesList', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeFleetCapacityInput' => [ 'type' => 'structure', 'members' => [ 'FleetIds' => [ 'shape' => 'FleetIdList', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeFleetCapacityOutput' => [ 'type' => 'structure', 'members' => [ 'FleetCapacity' => [ 'shape' => 'FleetCapacityList', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeFleetEventsInput' => [ 'type' => 'structure', 'required' => [ 'FleetId', ], 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'StartTime' => [ 'shape' => 'Timestamp', ], 'EndTime' => [ 'shape' => 'Timestamp', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeFleetEventsOutput' => [ 'type' => 'structure', 'members' => [ 'Events' => [ 'shape' => 'EventList', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeFleetPortSettingsInput' => [ 'type' => 'structure', 'required' => [ 'FleetId', ], 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], ], ], 'DescribeFleetPortSettingsOutput' => [ 'type' => 'structure', 'members' => [ 'InboundPermissions' => [ 'shape' => 'IpPermissionsList', ], ], ], 'DescribeFleetUtilizationInput' => [ 'type' => 'structure', 'members' => [ 'FleetIds' => [ 'shape' => 'FleetIdList', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeFleetUtilizationOutput' => [ 'type' => 'structure', 'members' => [ 'FleetUtilization' => [ 'shape' => 'FleetUtilizationList', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeGameSessionsInput' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'GameSessionId' => [ 'shape' => 'GameSessionId', ], 'AliasId' => [ 'shape' => 'AliasId', ], 'StatusFilter' => [ 'shape' => 'NonZeroAndMaxString', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribeGameSessionsOutput' => [ 'type' => 'structure', 'members' => [ 'GameSessions' => [ 'shape' => 'GameSessionList', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribePlayerSessionsInput' => [ 'type' => 'structure', 'members' => [ 'GameSessionId' => [ 'shape' => 'GameSessionId', ], 'PlayerId' => [ 'shape' => 'NonZeroAndMaxString', ], 'PlayerSessionId' => [ 'shape' => 'PlayerSessionId', ], 'PlayerSessionStatusFilter' => [ 'shape' => 'NonZeroAndMaxString', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'DescribePlayerSessionsOutput' => [ 'type' => 'structure', 'members' => [ 'PlayerSessions' => [ 'shape' => 'PlayerSessionList', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'EC2InstanceCounts' => [ 'type' => 'structure', 'members' => [ 'DESIRED' => [ 'shape' => 'WholeNumber', ], 'PENDING' => [ 'shape' => 'WholeNumber', ], 'ACTIVE' => [ 'shape' => 'WholeNumber', ], 'TERMINATING' => [ 'shape' => 'WholeNumber', ], ], ], 'EC2InstanceLimit' => [ 'type' => 'structure', 'members' => [ 'EC2InstanceType' => [ 'shape' => 'EC2InstanceType', ], 'CurrentInstances' => [ 'shape' => 'WholeNumber', ], 'InstanceLimit' => [ 'shape' => 'WholeNumber', ], ], ], 'EC2InstanceLimitList' => [ 'type' => 'list', 'member' => [ 'shape' => 'EC2InstanceLimit', ], ], 'EC2InstanceType' => [ 'type' => 'string', 'enum' => [ 't2.micro', 't2.small', 't2.medium', 't2.large', 'c3.large', 'c3.xlarge', 'c3.2xlarge', 'c3.4xlarge', 'c3.8xlarge', 'c4.large', 'c4.xlarge', 'c4.2xlarge', 'c4.4xlarge', 'c4.8xlarge', 'r3.large', 'r3.xlarge', 'r3.2xlarge', 'r3.4xlarge', 'r3.8xlarge', 'm3.medium', 'm3.large', 'm3.xlarge', 'm3.2xlarge', 'm4.large', 'm4.xlarge', 'm4.2xlarge', 'm4.4xlarge', 'm4.10xlarge', ], ], 'Event' => [ 'type' => 'structure', 'members' => [ 'EventId' => [ 'shape' => 'NonZeroAndMaxString', ], 'ResourceId' => [ 'shape' => 'NonZeroAndMaxString', ], 'EventCode' => [ 'shape' => 'EventCode', ], 'Message' => [ 'shape' => 'NonEmptyString', ], 'EventTime' => [ 'shape' => 'Timestamp', ], ], ], 'EventCode' => [ 'type' => 'string', 'enum' => [ 'GENERIC_EVENT', 'FLEET_CREATED', 'FLEET_DELETED', 'FLEET_SCALING_EVENT', 'FLEET_STATE_DOWNLOADING', 'FLEET_STATE_VALIDATING', 'FLEET_STATE_BUILDING', 'FLEET_STATE_ACTIVATING', 'FLEET_STATE_ACTIVE', 'FLEET_STATE_ERROR', 'FLEET_INITIALIZATION_FAILED', 'FLEET_BINARY_DOWNLOAD_FAILED', 'FLEET_VALIDATION_LAUNCH_PATH_NOT_FOUND', 'FLEET_VALIDATION_EXECUTABLE_RUNTIME_FAILURE', 'FLEET_VALIDATION_TIMED_OUT', 'FLEET_ACTIVATION_FAILED', 'FLEET_ACTIVATION_FAILED_NO_INSTANCES', ], ], 'EventList' => [ 'type' => 'list', 'member' => [ 'shape' => 'Event', ], ], 'FleetAttributes' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'Description' => [ 'shape' => 'NonZeroAndMaxString', ], 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'CreationTime' => [ 'shape' => 'Timestamp', ], 'TerminationTime' => [ 'shape' => 'Timestamp', ], 'Status' => [ 'shape' => 'FleetStatus', ], 'BuildId' => [ 'shape' => 'BuildId', ], 'ServerLaunchPath' => [ 'shape' => 'NonZeroAndMaxString', ], 'ServerLaunchParameters' => [ 'shape' => 'NonZeroAndMaxString', ], 'LogPaths' => [ 'shape' => 'StringList', ], ], ], 'FleetAttributesList' => [ 'type' => 'list', 'member' => [ 'shape' => 'FleetAttributes', ], ], 'FleetCapacity' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'InstanceType' => [ 'shape' => 'EC2InstanceType', ], 'InstanceCounts' => [ 'shape' => 'EC2InstanceCounts', ], ], ], 'FleetCapacityExceededException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'FleetCapacityList' => [ 'type' => 'list', 'member' => [ 'shape' => 'FleetCapacity', ], ], 'FleetId' => [ 'type' => 'string', 'pattern' => '^fleet-\\S+', ], 'FleetIdList' => [ 'type' => 'list', 'member' => [ 'shape' => 'FleetId', ], 'min' => 1, ], 'FleetStatus' => [ 'type' => 'string', 'enum' => [ 'NEW', 'DOWNLOADING', 'VALIDATING', 'BUILDING', 'ACTIVATING', 'ACTIVE', 'DELETING', 'ERROR', 'TERMINATED', ], ], 'FleetUtilization' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'ActiveGameSessionCount' => [ 'shape' => 'WholeNumber', ], 'CurrentPlayerSessionCount' => [ 'shape' => 'WholeNumber', ], 'MaximumPlayerSessionCount' => [ 'shape' => 'WholeNumber', ], ], ], 'FleetUtilizationList' => [ 'type' => 'list', 'member' => [ 'shape' => 'FleetUtilization', ], ], 'FreeText' => [ 'type' => 'string', ], 'GameProperty' => [ 'type' => 'structure', 'required' => [ 'Key', 'Value', ], 'members' => [ 'Key' => [ 'shape' => 'GamePropertyKey', ], 'Value' => [ 'shape' => 'GamePropertyValue', ], ], ], 'GamePropertyKey' => [ 'type' => 'string', 'max' => 32, ], 'GamePropertyList' => [ 'type' => 'list', 'member' => [ 'shape' => 'GameProperty', ], 'max' => 16, ], 'GamePropertyValue' => [ 'type' => 'string', 'max' => 96, ], 'GameSession' => [ 'type' => 'structure', 'members' => [ 'GameSessionId' => [ 'shape' => 'GameSessionId', ], 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'FleetId' => [ 'shape' => 'FleetId', ], 'CreationTime' => [ 'shape' => 'Timestamp', ], 'TerminationTime' => [ 'shape' => 'Timestamp', ], 'CurrentPlayerSessionCount' => [ 'shape' => 'WholeNumber', ], 'MaximumPlayerSessionCount' => [ 'shape' => 'WholeNumber', ], 'Status' => [ 'shape' => 'GameSessionStatus', ], 'GameProperties' => [ 'shape' => 'GamePropertyList', ], 'IpAddress' => [ 'shape' => 'IpAddress', ], 'PlayerSessionCreationPolicy' => [ 'shape' => 'PlayerSessionCreationPolicy', ], ], ], 'GameSessionFullException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'GameSessionId' => [ 'type' => 'string', 'pattern' => '^(gamei-|gsess-)\\S+', ], 'GameSessionList' => [ 'type' => 'list', 'member' => [ 'shape' => 'GameSession', ], ], 'GameSessionStatus' => [ 'type' => 'string', 'enum' => [ 'ACTIVE', 'ACTIVATING', 'TERMINATED', 'TERMINATING', ], ], 'GetGameSessionLogUrlInput' => [ 'type' => 'structure', 'required' => [ 'GameSessionId', ], 'members' => [ 'GameSessionId' => [ 'shape' => 'GameSessionId', ], ], ], 'GetGameSessionLogUrlOutput' => [ 'type' => 'structure', 'members' => [ 'PreSignedUrl' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'InternalServiceException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, 'fault' => true, ], 'InvalidFleetStatusException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'InvalidGameSessionStatusException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'InvalidRequestException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'IpAddress' => [ 'type' => 'string', ], 'IpPermission' => [ 'type' => 'structure', 'required' => [ 'FromPort', 'ToPort', 'IpRange', 'Protocol', ], 'members' => [ 'FromPort' => [ 'shape' => 'PortNumber', ], 'ToPort' => [ 'shape' => 'PortNumber', ], 'IpRange' => [ 'shape' => 'NonBlankString', ], 'Protocol' => [ 'shape' => 'IpProtocol', ], ], ], 'IpPermissionsList' => [ 'type' => 'list', 'member' => [ 'shape' => 'IpPermission', ], 'max' => 50, ], 'IpProtocol' => [ 'type' => 'string', 'enum' => [ 'TCP', 'UDP', ], ], 'LimitExceededException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'ListAliasesInput' => [ 'type' => 'structure', 'members' => [ 'RoutingStrategyType' => [ 'shape' => 'RoutingStrategyType', ], 'Name' => [ 'shape' => 'NonEmptyString', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonEmptyString', ], ], ], 'ListAliasesOutput' => [ 'type' => 'structure', 'members' => [ 'Aliases' => [ 'shape' => 'AliasList', ], 'NextToken' => [ 'shape' => 'NonEmptyString', ], ], ], 'ListBuildsInput' => [ 'type' => 'structure', 'members' => [ 'Status' => [ 'shape' => 'BuildStatus', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonEmptyString', ], ], ], 'ListBuildsOutput' => [ 'type' => 'structure', 'members' => [ 'Builds' => [ 'shape' => 'BuildList', ], 'NextToken' => [ 'shape' => 'NonEmptyString', ], ], ], 'ListFleetsInput' => [ 'type' => 'structure', 'members' => [ 'BuildId' => [ 'shape' => 'BuildId', ], 'Limit' => [ 'shape' => 'PositiveInteger', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'ListFleetsOutput' => [ 'type' => 'structure', 'members' => [ 'FleetIds' => [ 'shape' => 'FleetIdList', ], 'NextToken' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'NonBlankString' => [ 'type' => 'string', 'pattern' => '[^\\s]+', ], 'NonEmptyString' => [ 'type' => 'string', 'min' => 1, ], 'NonZeroAndMaxString' => [ 'type' => 'string', 'min' => 1, 'max' => 1024, ], 'NotFoundException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'PlayerIdList' => [ 'type' => 'list', 'member' => [ 'shape' => 'NonZeroAndMaxString', ], 'min' => 1, 'max' => 25, ], 'PlayerSession' => [ 'type' => 'structure', 'members' => [ 'PlayerSessionId' => [ 'shape' => 'PlayerSessionId', ], 'PlayerId' => [ 'shape' => 'NonZeroAndMaxString', ], 'GameSessionId' => [ 'shape' => 'GameSessionId', ], 'FleetId' => [ 'shape' => 'FleetId', ], 'CreationTime' => [ 'shape' => 'Timestamp', ], 'TerminationTime' => [ 'shape' => 'Timestamp', ], 'Status' => [ 'shape' => 'PlayerSessionStatus', ], 'IpAddress' => [ 'shape' => 'IpAddress', ], ], ], 'PlayerSessionCreationPolicy' => [ 'type' => 'string', 'enum' => [ 'ACCEPT_ALL', 'DENY_ALL', ], ], 'PlayerSessionId' => [ 'type' => 'string', 'pattern' => '^psess-\\S+', ], 'PlayerSessionList' => [ 'type' => 'list', 'member' => [ 'shape' => 'PlayerSession', ], ], 'PlayerSessionStatus' => [ 'type' => 'string', 'enum' => [ 'RESERVED', 'ACTIVE', 'COMPLETED', 'TIMEDOUT', ], ], 'PortNumber' => [ 'type' => 'integer', 'min' => 1025, 'max' => 60000, ], 'PositiveInteger' => [ 'type' => 'integer', 'min' => 1, ], 'PositiveLong' => [ 'type' => 'long', 'min' => 1, ], 'RequestUploadCredentialsInput' => [ 'type' => 'structure', 'required' => [ 'BuildId', ], 'members' => [ 'BuildId' => [ 'shape' => 'BuildId', ], ], ], 'RequestUploadCredentialsOutput' => [ 'type' => 'structure', 'members' => [ 'UploadCredentials' => [ 'shape' => 'AwsCredentials', ], 'StorageLocation' => [ 'shape' => 'S3Location', ], ], ], 'ResolveAliasInput' => [ 'type' => 'structure', 'required' => [ 'AliasId', ], 'members' => [ 'AliasId' => [ 'shape' => 'AliasId', ], ], ], 'ResolveAliasOutput' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], ], ], 'RoutingStrategy' => [ 'type' => 'structure', 'members' => [ 'Type' => [ 'shape' => 'RoutingStrategyType', ], 'FleetId' => [ 'shape' => 'FleetId', ], 'Message' => [ 'shape' => 'FreeText', ], ], ], 'RoutingStrategyType' => [ 'type' => 'string', 'enum' => [ 'SIMPLE', 'TERMINAL', ], ], 'S3Location' => [ 'type' => 'structure', 'members' => [ 'Bucket' => [ 'shape' => 'NonEmptyString', ], 'Key' => [ 'shape' => 'NonEmptyString', ], ], ], 'StringList' => [ 'type' => 'list', 'member' => [ 'shape' => 'NonZeroAndMaxString', ], ], 'TerminalRoutingStrategyException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'Timestamp' => [ 'type' => 'timestamp', ], 'UnauthorizedException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'NonEmptyString', ], ], 'exception' => true, ], 'UpdateAliasInput' => [ 'type' => 'structure', 'required' => [ 'AliasId', ], 'members' => [ 'AliasId' => [ 'shape' => 'AliasId', ], 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'Description' => [ 'shape' => 'NonZeroAndMaxString', ], 'RoutingStrategy' => [ 'shape' => 'RoutingStrategy', ], ], ], 'UpdateAliasOutput' => [ 'type' => 'structure', 'members' => [ 'Alias' => [ 'shape' => 'Alias', ], ], ], 'UpdateBuildInput' => [ 'type' => 'structure', 'required' => [ 'BuildId', ], 'members' => [ 'BuildId' => [ 'shape' => 'BuildId', ], 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'Version' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'UpdateBuildOutput' => [ 'type' => 'structure', 'members' => [ 'Build' => [ 'shape' => 'Build', ], ], ], 'UpdateFleetAttributesInput' => [ 'type' => 'structure', 'required' => [ 'FleetId', ], 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'Description' => [ 'shape' => 'NonZeroAndMaxString', ], ], ], 'UpdateFleetAttributesOutput' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], ], ], 'UpdateFleetCapacityInput' => [ 'type' => 'structure', 'required' => [ 'FleetId', 'DesiredInstances', ], 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'DesiredInstances' => [ 'shape' => 'WholeNumber', ], ], ], 'UpdateFleetCapacityOutput' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], ], ], 'UpdateFleetPortSettingsInput' => [ 'type' => 'structure', 'required' => [ 'FleetId', ], 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], 'InboundPermissionAuthorizations' => [ 'shape' => 'IpPermissionsList', ], 'InboundPermissionRevocations' => [ 'shape' => 'IpPermissionsList', ], ], ], 'UpdateFleetPortSettingsOutput' => [ 'type' => 'structure', 'members' => [ 'FleetId' => [ 'shape' => 'FleetId', ], ], ], 'UpdateGameSessionInput' => [ 'type' => 'structure', 'required' => [ 'GameSessionId', ], 'members' => [ 'GameSessionId' => [ 'shape' => 'GameSessionId', ], 'MaximumPlayerSessionCount' => [ 'shape' => 'WholeNumber', ], 'Name' => [ 'shape' => 'NonZeroAndMaxString', ], 'PlayerSessionCreationPolicy' => [ 'shape' => 'PlayerSessionCreationPolicy', ], ], ], 'UpdateGameSessionOutput' => [ 'type' => 'structure', 'members' => [ 'GameSession' => [ 'shape' => 'GameSession', ], ], ], 'WholeNumber' => [ 'type' => 'integer', 'min' => 0, ], ],];
